<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(private CartService $cartService) {}

    public function createFromCart(array $checkoutData, ?Coupon $coupon = null): Order
    {
        return DB::transaction(function () use ($checkoutData, $coupon) {
            $items    = $this->cartService->getItems();
            $settings = StoreSetting::current();

            if ($items->isEmpty()) {
                throw new \RuntimeException('Cart is empty.');
            }

            $subtotal      = 0;
            $couponDiscount = 0;
            $shippingCost  = (float) ($settings->shipping_cost ?? 0);
            $taxRate       = (float) ($settings->tax_rate ?? 0);

            // Calculate subtotal
            foreach ($items as $item) {
                $subtotal += $item->quantity * ($item->variant?->effective_price ?? 0);
            }

            // Free shipping threshold
            if ($settings->free_shipping_threshold && $subtotal >= $settings->free_shipping_threshold) {
                $shippingCost = 0;
            }

            // Coupon discount
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_order_amount) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }

            $taxableAmount = $subtotal - $couponDiscount + $shippingCost;
            $taxAmount     = $taxableAmount * ($taxRate / 100);
            $total         = $taxableAmount + $taxAmount;

            $order = Order::create([
                'user_id'         => auth()->id(),
                'company_id'      => 1,
                'order_number'    => Order::generateOrderNumber(),
                'status'          => 'pending',
                'payment_status'  => 'pending',
                'payment_method'  => $checkoutData['payment_method'] ?? 'cod',
                'subtotal'        => $subtotal,
                'coupon_code'     => $coupon?->code,
                'coupon_discount' => $couponDiscount,
                'shipping_cost'   => $shippingCost,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $total,
                'shipping_address'=> $checkoutData['shipping_address'],
                'billing_address' => $checkoutData['billing_address'] ?? $checkoutData['shipping_address'],
                'notes'           => $checkoutData['notes'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                $variant = $item->variant;
                $product = $item->product;
                $price   = $variant?->effective_price ?? 0;

                $order->orderItems()->create([
                    'product_id'         => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name_ar'    => $product?->name_ar,
                    'product_name_en'    => $product?->name_en,
                    'variant_name'       => $variant?->name_ar ?? $variant?->name_en,
                    'sku'                => $variant?->sku,
                    'product_image'      => $product?->main_image,
                    'quantity'           => $item->quantity,
                    'unit_price'         => $price,
                    'total_price'        => $item->quantity * $price,
                    'product_snapshot'   => [
                        'name_ar'     => $product?->name_ar,
                        'name_en'     => $product?->name_en,
                        'price'       => $price,
                        'sku'         => $variant?->sku,
                    ],
                ]);

                // Decrement stock
                if ($variant) {
                    $variant->decrement('stock_quantity', $item->quantity);
                }
            }

            // Record coupon usage
            if ($coupon && $couponDiscount > 0) {
                CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'user_id'         => auth()->id(),
                    'order_id'        => $order->id,
                    'discount_amount' => $couponDiscount,
                ]);
                $coupon->increment('used_count');
            }

            // Initial status history
            OrderStatusHistory::create([
                'order_id'  => $order->id,
                'to_status' => 'pending',
                'notes'     => 'Order placed',
            ]);

            // Clear cart
            $this->cartService->clear();

            return $order->fresh(['orderItems']);
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $notes = null, ?int $adminId = null): Order
    {
        $oldStatus = $order->status;

        $timestamps = [
            'shipped'   => 'shipped_at',
            'delivered' => 'delivered_at',
            'cancelled' => 'cancelled_at',
        ];

        $updateData = ['status' => $newStatus];
        if (isset($timestamps[$newStatus])) {
            $updateData[$timestamps[$newStatus]] = now();
        }

        $order->update($updateData);

        OrderStatusHistory::create([
            'order_id'    => $order->id,
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
            'notes'       => $notes,
            'created_by'  => $adminId ?? auth()->id(),
        ]);

        return $order->fresh();
    }

    public function updatePaymentStatus(Order $order, string $status): Order
    {
        $order->update(['payment_status' => $status]);
        return $order->fresh();
    }
}

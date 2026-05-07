<?php

namespace App\Http\Controllers\API\V1\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = auth()->user();
        $cart = Cart::with('cartItems.productVariant.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        // Simplification for MVP: We assume the cart only contains items from ONE company.
        // If there are multiple, we create one order per company. Let's group by company.
        $companyOrders = [];

        DB::transaction(function () use ($cart, $request, $user, &$companyOrders) {
            $groupedItems = $cart->cartItems->groupBy('productVariant.product.company_id');

            foreach ($groupedItems as $companyId => $items) {
                $totalAmount = $items->sum(fn ($item) => $item->quantity * $item->productVariant->price);

                $order = Order::create([
                    'user_id' => $user->id,
                    'company_id' => $companyId,
                    'status' => 'pending',
                    'total_amount' => $totalAmount,
                    'shipping_address' => $request->input('shipping_address'),
                    'notes' => $request->input('notes'),
                ]);

                $orderItemsData = $items->map(function ($item) {
                    $variant = $item->productVariant;
                    
                    // Reduce stock
                    $variant->decrement('stock_quantity', $item->quantity);

                    return [
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $variant->price,
                        'total_price' => $variant->price * $item->quantity,
                    ];
                });

                $order->orderItems()->createMany($orderItemsData->toArray());

                $order->load(['orderItems.product', 'orderItems.productVariant', 'company']);
                $companyOrders[] = $order;
            }

            // Clear cart
            $cart->cartItems()->delete();
        });

        return response()->json([
            'message' => 'Order placed successfully.',
            'orders' => OrderResource::collection($companyOrders),
        ], 201);
    }
}

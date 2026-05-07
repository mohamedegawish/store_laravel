<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private CouponService $couponService,
    ) {}

    public function index()
    {
        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('store.cart')->with('error', 'سلة التسوق فارغة');
        }

        $subtotal  = $this->cartService->getSubtotal();
        $settings  = StoreSetting::current();
        $user      = auth()->user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        $couponCode     = session('coupon_code');
        $coupon         = null;
        $couponDiscount = 0;

        if ($couponCode) {
            $result = $this->couponService->validate($couponCode, $subtotal, $user->id);
            if ($result['valid']) {
                $coupon         = $result['coupon'];
                $couponDiscount = $result['discount'];
            }
        }

        $shippingCost = (float) ($settings->shipping_cost ?? 0);
        if ($settings->free_shipping_threshold && $subtotal >= $settings->free_shipping_threshold) {
            $shippingCost = 0;
        }

        $taxRate   = (float) ($settings->tax_rate ?? 0);
        $taxAmount = ($subtotal - $couponDiscount + $shippingCost) * ($taxRate / 100);
        $total     = $subtotal - $couponDiscount + $shippingCost + $taxAmount;

        return view('store.checkout', compact(
            'items', 'subtotal', 'settings', 'addresses',
            'coupon', 'couponDiscount', 'shippingCost', 'taxAmount', 'total'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'          => 'required|string|max:255',
            'shipping_phone'         => 'required|string|max:30',
            'shipping_address_line1' => 'required|string|max:500',
            'shipping_city'          => 'required|string|max:100',
            'payment_method'         => 'required|in:cod,online',
            'notes'                  => 'nullable|string|max:500',
            'save_address'           => 'nullable|boolean',
        ]);

        $shippingAddress = [
            'name'          => $request->shipping_name,
            'phone'         => $request->shipping_phone,
            'address_line1' => $request->shipping_address_line1,
            'address_line2' => $request->shipping_address_line2,
            'city'          => $request->shipping_city,
            'state'         => $request->shipping_state,
            'country'       => 'SA',
        ];

        if ($request->boolean('save_address')) {
            auth()->user()->addresses()->create(array_merge($shippingAddress, [
                'is_default' => auth()->user()->addresses()->count() === 0,
            ]));
        }

        $coupon     = null;
        $couponCode = session('coupon_code');
        if ($couponCode) {
            $result = $this->couponService->validate($couponCode, $this->cartService->getSubtotal(), auth()->id());
            if ($result['valid']) {
                $coupon = $result['coupon'];
            }
        }

        try {
            $order = $this->orderService->createFromCart([
                'shipping_address' => $shippingAddress,
                'payment_method'   => $request->payment_method,
                'notes'            => $request->notes,
            ], $coupon);

            session()->forget('coupon_code');

            return redirect()->route('store.order.success', $order->order_number)
                ->with('success', 'تم تقديم طلبك بنجاح!');
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء معالجة طلبك. حاول مرة أخرى.');
        }
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['orderItems.product'])
            ->firstOrFail();

        return view('store.order-success', compact('order'));
    }
}

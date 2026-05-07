<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CouponService $couponService,
    ) {}

    public function index()
    {
        $settings       = StoreSetting::current();
        $items          = $this->cartService->getItems();
        $subtotal       = $this->cartService->getSubtotal();
        $couponCode     = session('coupon_code');
        $couponDiscount = 0;

        if ($couponCode) {
            $result = $this->couponService->validate($couponCode, $subtotal, auth()->id());
            if ($result['valid']) $couponDiscount = $result['discount'];
            else session()->forget('coupon_code');
        }

        $shippingCost = $settings && $settings->free_shipping_threshold && ($subtotal - $couponDiscount) >= $settings->free_shipping_threshold
            ? 0
            : ($settings?->shipping_cost ?? 0);

        $taxRate  = $settings?->tax_rate ?? 0;
        $taxable  = $subtotal - $couponDiscount + $shippingCost;
        $taxAmount = $taxRate > 0 ? round($taxable * ($taxRate / 100), 2) : 0;
        $total    = $subtotal - $couponDiscount + $shippingCost + $taxAmount;

        return view('store.cart', compact('items', 'subtotal', 'couponDiscount', 'shippingCost', 'taxAmount', 'total'));
    }

    public function mini()
    {
        $items    = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();
        $count    = $this->cartService->getItemCount();

        $html = view('store.partials.cart-mini', compact('items'))->render();

        return response()->json([
            'html'    => $html,
            'total'   => number_format($subtotal, 2),
            'count'   => $count,
            'success' => true,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'nullable|integer|min:1|max:999',
        ]);

        $variantId = $request->variant_id;

        if (!$variantId) {
            $product   = Product::with('activeVariants')->findOrFail($request->product_id);
            $variant   = $product->defaultVariant ?? $product->activeVariants->first();
            $variantId = $variant?->id;
        }

        if (!$variantId) {
            return $this->jsonOrRedirect($request, false, 'المنتج غير متوفر حالياً');
        }

        $this->cartService->addItem($variantId, $request->get('quantity', 1));
        $count = $this->cartService->getItemCount();
        session(['cart_count' => $count]);

        return $this->jsonOrRedirect($request, true, 'تمت الإضافة للسلة', ['cart_count' => $count]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|integer',
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        $item = CartItem::find($request->item_id);
        if ($item && $item->cart_id === $this->cartService->getCart()->id) {
            if ($request->quantity <= 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $request->quantity]);
            }
        }

        $count = $this->cartService->getItemCount();
        session(['cart_count' => $count]);

        return $this->jsonOrRedirect($request, true, 'تم التحديث', [
            'cart_count' => $count,
            'subtotal'   => number_format($this->cartService->getSubtotal(), 2),
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate(['item_id' => 'required|integer']);

        $item = CartItem::find($request->item_id);
        if ($item && $item->cart_id === $this->cartService->getCart()->id) {
            $item->delete();
        }

        $count = $this->cartService->getItemCount();
        session(['cart_count' => $count]);

        return $this->jsonOrRedirect($request, true, 'تم الحذف', ['cart_count' => $count]);
    }

    public function clear(Request $request)
    {
        $this->cartService->clear();
        session(['cart_count' => 0]);
        return redirect()->route('store.cart')->with('success', 'تم تفريغ السلة');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $subtotal = $this->cartService->getSubtotal();
        $result   = $this->couponService->validate($request->code, $subtotal, auth()->id());

        if ($result['valid']) {
            session(['coupon_code' => $request->code]);
            return back()->with('coupon_success', 'تم تطبيق كود الخصم');
        }

        return back()->with('coupon_error', $result['message']);
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return back()->with('success', 'تم إزالة كود الخصم');
    }

    private function jsonOrRedirect(Request $request, bool $success, string $message, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge(['success' => $success, 'message' => $message], $extra));
        }
        return $success ? back()->with('success', $message) : back()->with('error', $message);
    }
}

<?php

namespace App\Http\Controllers\API\V1\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function show(): CartResource
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['cartItems.productVariant.product']);

        return new CartResource($cart);
    }

    public function syncItem(SyncCartRequest $request): CartResource
    {
        $cart = $this->getOrCreateCart();
        $variantId = $request->integer('product_variant_id');
        $quantity = $request->integer('quantity');

        CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_variant_id' => $variantId],
            ['quantity' => $quantity]
        );

        $cart->load(['cartItems.productVariant.product']);

        return new CartResource($cart);
    }

    public function removeItem(int $variantId): JsonResponse
    {
        $cart = tap($this->getOrCreateCart(), function ($cart) use ($variantId) {
            $cart->cartItems()->where('product_variant_id', $variantId)->delete();
        });

        return response()->json(['message' => 'Item removed from cart.']);
    }

    public function clear(): JsonResponse
    {
        $this->getOrCreateCart()->cartItems()->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }

    private function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }
}

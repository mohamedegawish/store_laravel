<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        if (auth()->check()) {
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
            // Merge session cart if exists
            $sessionKey = session('guest_cart_id');
            if ($sessionKey) {
                $guestCart = Cart::where('session_id', $sessionKey)->first();
                if ($guestCart) {
                    $this->mergeGuestCart($guestCart, $cart);
                    $guestCart->delete();
                    Session::forget('guest_cart_id');
                }
            }
            return $cart;
        }

        $sessionKey = session('guest_cart_id');
        if (!$sessionKey) {
            $cart = Cart::create(['session_id' => uniqid('guest_', true)]);
            session(['guest_cart_id' => $cart->session_id]);
            return $cart;
        }
        return Cart::firstOrCreate(['session_id' => $sessionKey]);
    }

    public function addItem(int $variantId, int $quantity = 1): CartItem
    {
        $cart    = $this->getCart();
        $variant = ProductVariant::findOrFail($variantId);

        $item = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;
            $newQty = min($newQty, $variant->stock_quantity);
            $item->update(['quantity' => $newQty]);
        } else {
            $item = $cart->items()->create([
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variantId,
                'quantity'           => min($quantity, $variant->stock_quantity),
            ]);
        }

        return $item;
    }

    public function updateItem(int $variantId, int $quantity): void
    {
        $cart    = $this->getCart();
        $variant = ProductVariant::find($variantId);

        if ($quantity <= 0) {
            $this->removeItem($variantId);
            return;
        }

        $cart->items()->where('product_variant_id', $variantId)->update([
            'quantity' => min($quantity, $variant->stock_quantity ?? $quantity),
        ]);
    }

    public function removeItem(int $variantId): void
    {
        $cart = $this->getCart();
        $cart->items()->where('product_variant_id', $variantId)->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    public function getItems(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getCart()
            ->items()
            ->with(['product.category', 'variant'])
            ->get();
    }

    public function getItemCount(): int
    {
        return $this->getCart()->items()->sum('quantity');
    }

    public function getSubtotal(): float
    {
        return (float) $this->getItems()->sum(fn($item) =>
            $item->quantity * ($item->variant?->effective_price ?? 0)
        );
    }

    private function mergeGuestCart(Cart $guestCart, Cart $userCart): void
    {
        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $userCart->items()->create([
                    'product_id'         => $guestItem->product_id,
                    'product_variant_id' => $guestItem->product_variant_id,
                    'quantity'           => $guestItem->quantity,
                ]);
            }
        }
    }
}

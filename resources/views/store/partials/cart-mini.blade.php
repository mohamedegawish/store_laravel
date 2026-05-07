@if($items->count())
@foreach($items as $item)
<div class="cart-item">
    <img src="{{ $item->variant?->image ? asset('storage/'.$item->variant->image) : ($item->product?->main_image ? asset('storage/'.$item->product->main_image) : 'https://via.placeholder.com/64x64?text=?') }}"
         alt="{{ $item->variant?->product_name_ar ?? '' }}">
    <div class="cart-item-info">
        <div class="cart-item-name">{{ $item->product?->name ?? '—' }}</div>
        @if($item->variant?->name)
        <div class="cart-item-variant">{{ $item->variant->name }}</div>
        @endif
        <div class="cart-item-price">{{ number_format(($item->variant?->effective_price ?? 0) * $item->quantity, 2) }} ر.س</div>
        <div class="cart-item-qty">
            <button class="cart-qty-btn" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity - 1 }})">−</button>
            <span class="cart-qty-val">{{ $item->quantity }}</span>
            <button class="cart-qty-btn" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
            <button class="cart-item-remove" onclick="removeCartItem({{ $item->id }})" title="حذف">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
@endforeach
@else
<div style="text-align:center;padding:40px;color:var(--text-muted)">
    <i class="fas fa-shopping-bag" style="font-size:2.5rem;opacity:.3;margin-bottom:12px"></i>
    <p>السلة فارغة</p>
</div>
@endif

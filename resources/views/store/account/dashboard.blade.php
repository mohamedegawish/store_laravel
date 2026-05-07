@extends('store.account.layout')
@section('title', 'حسابي')

@section('account_content')

<h2 style="font-size:1.3rem;font-weight:800;margin-bottom:20px">مرحباً، {{ auth()->user()->name }} 👋</h2>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    @foreach([
        ['fas fa-box','طلباتي', $ordersCount, route('store.account.orders'),'var(--primary)'],
        ['fas fa-heart','المفضلة', $wishlistCount, route('store.account.wishlist'),'var(--danger)'],
        ['fas fa-star','تقييماتي', $reviewsCount, '#','var(--warning)'],
    ] as [$icon,$label,$count,$link,$color])
    <a href="{{ $link }}" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px;display:flex;align-items:center;gap:14px;transition:box-shadow .2s"
       onmouseover="this.style.boxShadow='var(--shadow)'" onmouseout="this.style.boxShadow='none'">
        <div style="width:48px;height:48px;border-radius:var(--radius-sm);background:color-mix(in srgb, {{ $color }} 15%, #fff);display:flex;align-items:center;justify-content:center;color:{{ $color }};font-size:1.2rem;flex-shrink:0">
            <i class="{{ $icon }}"></i>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900">{{ $count }}</div>
            <div style="font-size:.82rem;color:var(--text-muted)">{{ $label }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Recent Orders --}}
@if($recentOrders->count())
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <span style="font-weight:700;font-size:.95rem">آخر الطلبات</span>
        <a href="{{ route('store.account.orders') }}" style="font-size:.82rem;color:var(--primary)">عرض الكل</a>
    </div>
    @foreach($recentOrders as $order)
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
        <div>
            <div style="font-weight:700;font-size:.9rem">#{{ $order->order_number }}</div>
            <div style="font-size:.78rem;color:var(--text-muted)">{{ $order->created_at->format('d/m/Y') }} — {{ $order->orderItems->count() }} منتج</div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <span style="font-weight:700;font-size:.9rem">{{ number_format($order->total_amount,2) }} ر.س</span>
            <span class="badge badge-{{ $order->status_color }}" style="font-size:.75rem">{{ $order->status_label }}</span>
            <a href="{{ route('store.account.order', $order) }}" style="font-size:.8rem;color:var(--primary)">تفاصيل</a>
        </div>
    </div>
    @endforeach
</div>
@else
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:40px;text-align:center;color:var(--text-muted)">
    <i class="fas fa-box" style="font-size:2.5rem;opacity:.25;margin-bottom:12px"></i>
    <p>لا توجد طلبات بعد</p>
    <a href="{{ route('store.products') }}" class="btn btn-primary btn-sm" style="margin-top:14px">تسوق الآن</a>
</div>
@endif
@endsection

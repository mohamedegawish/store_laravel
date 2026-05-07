@extends('store.account.layout')
@section('title', 'طلباتي')

@section('account_content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <h2 style="font-size:1.2rem;font-weight:800">طلباتي</h2>
    <span style="font-size:.85rem;color:var(--text-muted)">{{ $orders->total() }} طلب</span>
</div>

@forelse($orders as $order)
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:16px;overflow:hidden">
    <div style="padding:16px 20px;background:var(--surface);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
        <div>
            <span style="font-weight:800;font-size:.95rem">#{{ $order->order_number }}</span>
            <span style="font-size:.78rem;color:var(--text-muted);margin-inline-start:12px">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="badge badge-{{ $order->status_color }}" style="font-size:.78rem">{{ $order->status_label }}</span>
            <span class="badge badge-{{ $order->payment_status_color }}" style="font-size:.78rem">{{ $order->payment_status_label }}</span>
        </div>
    </div>
    <div style="padding:16px 20px">
        <div style="display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap">
            @foreach($order->orderItems->take(3) as $item)
            <div style="display:flex;align-items:center;gap:8px;background:var(--surface);border-radius:var(--radius-sm);padding:8px 12px">
                @if($item->product_image)
                <img src="{{ asset('storage/'.$item->product_image) }}" style="width:32px;height:32px;border-radius:4px;object-fit:cover">
                @endif
                <span style="font-size:.82rem;font-weight:500">{{ Str::limit($item->product_name_ar, 25) }}</span>
                <span style="font-size:.78rem;color:var(--text-muted)">×{{ $item->quantity }}</span>
            </div>
            @endforeach
            @if($order->orderItems->count() > 3)
            <div style="display:flex;align-items:center;padding:8px 12px;font-size:.82rem;color:var(--text-muted)">+{{ $order->orderItems->count() - 3 }} أخرى</div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
            <div style="font-weight:800;font-size:1rem;color:var(--primary)">{{ number_format($order->total_amount,2) }} ر.س</div>
            <a href="{{ route('store.account.order', $order) }}" class="btn btn-outline btn-sm">عرض التفاصيل</a>
        </div>
    </div>
</div>
@empty
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:60px;text-align:center;color:var(--text-muted)">
    <i class="fas fa-box" style="font-size:3rem;opacity:.2;margin-bottom:16px"></i>
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:8px">لا توجد طلبات</h3>
    <a href="{{ route('store.products') }}" class="btn btn-primary btn-sm" style="margin-top:12px">تسوق الآن</a>
</div>
@endforelse

@if($orders->hasPages())
<div class="pagination">{{ $orders->links() }}</div>
@endif
@endsection

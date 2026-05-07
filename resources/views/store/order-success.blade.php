@extends('layouts.store')
@section('title', 'تم استلام طلبك!')

@section('content')
<div class="container" style="padding-top:60px;padding-bottom:80px;max-width:700px">

    <div style="text-align:center;margin-bottom:48px">
        <div style="width:80px;height:80px;background:#D1FAE5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--success)">
            <i class="fas fa-check"></i>
        </div>
        <h1 style="font-size:2rem;font-weight:900;margin-bottom:10px">تم استلام طلبك!</h1>
        <p style="color:var(--text-muted);font-size:1rem">شكراً لك على تسوقك معنا. سيتم التواصل معك قريباً.</p>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:24px">
        <div style="background:var(--primary);color:#fff;padding:20px 24px;display:flex;justify-content:space-between;align-items:center">
            <div>
                <div style="font-size:.82rem;opacity:.8;margin-bottom:4px">رقم الطلب</div>
                <div style="font-size:1.3rem;font-weight:800">#{{ $order->order_number }}</div>
            </div>
            <div style="text-align:end">
                <div style="font-size:.82rem;opacity:.8;margin-bottom:4px">التاريخ</div>
                <div style="font-weight:600">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div style="padding:24px">
            <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
                <thead>
                    <tr style="background:var(--surface)">
                        <th style="padding:10px 14px;font-size:.82rem;color:var(--text-muted);text-align:right;font-weight:600">المنتج</th>
                        <th style="padding:10px 14px;font-size:.82rem;color:var(--text-muted);text-align:right;font-weight:600">الكمية</th>
                        <th style="padding:10px 14px;font-size:.82rem;color:var(--text-muted);text-align:right;font-weight:600">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $item)
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:12px 14px;font-size:.88rem">
                        <div style="font-weight:600">{{ $item->product_name_ar }}</div>
                        @if($item->variant_name)<div style="font-size:.78rem;color:var(--text-muted)">{{ $item->variant_name }}</div>@endif
                    </td>
                    <td style="padding:12px 14px;font-size:.88rem;color:var(--text-muted)">× {{ $item->quantity }}</td>
                    <td style="padding:12px 14px;font-size:.88rem;font-weight:700">{{ number_format($item->total_price, 2) }} ر.س</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                    @if($order->coupon_discount > 0)
                    <tr><td colspan="2" style="padding:10px 14px;text-align:end;color:var(--success)">خصم ({{ $order->coupon_code }})</td><td style="padding:10px 14px;font-weight:600;color:var(--success)">- {{ number_format($order->coupon_discount,2) }} ر.س</td></tr>
                    @endif
                    @if($order->shipping_cost > 0)
                    <tr><td colspan="2" style="padding:10px 14px;text-align:end;color:var(--text-muted)">الشحن</td><td style="padding:10px 14px;font-weight:600">{{ number_format($order->shipping_cost,2) }} ر.س</td></tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr><td colspan="2" style="padding:10px 14px;text-align:end;color:var(--text-muted)">الضريبة</td><td style="padding:10px 14px;font-weight:600">{{ number_format($order->tax_amount,2) }} ر.س</td></tr>
                    @endif
                    <tr style="background:var(--primary-light)">
                        <td colspan="2" style="padding:14px;font-weight:800;font-size:1rem">الإجمالي</td>
                        <td style="padding:14px;font-weight:800;font-size:1rem;color:var(--primary)">{{ number_format($order->total_amount, 2) }} ر.س</td>
                    </tr>
                </tfoot>
            </table>

            @if($order->shipping_address)
            @php $addr = $order->shipping_address; @endphp
            <div style="background:var(--surface);border-radius:var(--radius-sm);padding:16px">
                <div style="font-weight:700;font-size:.85rem;margin-bottom:8px"><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-inline-end:6px"></i> عنوان التوصيل</div>
                <div style="font-size:.88rem;color:var(--text-muted);line-height:1.8">
                    {{ $addr['name'] ?? '' }} — {{ $addr['phone'] ?? '' }}<br>
                    {{ $addr['address_line1'] ?? '' }}, {{ $addr['city'] ?? '' }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
        @auth
        <a href="{{ route('store.account.orders') }}" class="btn btn-outline">
            <i class="fas fa-box"></i> طلباتي
        </a>
        @endauth
        <a href="{{ route('store.home') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> متابعة التسوق
        </a>
    </div>

</div>
@endsection

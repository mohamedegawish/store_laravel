@extends('store.account.layout')
@section('title', 'طلب #' . $order->order_number)

@section('account_content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <h2 style="font-size:1.2rem;font-weight:800">طلب #{{ $order->order_number }}</h2>
    <div style="display:flex;gap:10px">
        <span class="badge badge-{{ $order->status_color }}" style="font-size:.82rem;padding:6px 14px">{{ $order->status_label }}</span>
        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-outline btn-sm">
            <i class="fas fa-file-pdf"></i> الفاتورة
        </a>
    </div>
</div>

<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:700;font-size:.9rem;background:var(--surface)">
        <i class="fas fa-shopping-cart" style="color:var(--primary);margin-inline-end:8px"></i> المنتجات
    </div>
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="background:var(--surface)">
                <th style="padding:10px 16px;font-size:.8rem;font-weight:600;color:var(--text-muted);text-align:right">المنتج</th>
                <th style="padding:10px 16px;font-size:.8rem;font-weight:600;color:var(--text-muted);text-align:right">السعر</th>
                <th style="padding:10px 16px;font-size:.8rem;font-weight:600;color:var(--text-muted);text-align:right">الكمية</th>
                <th style="padding:10px 16px;font-size:.8rem;font-weight:600;color:var(--text-muted);text-align:right">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order->orderItems as $item)
        <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:14px 16px">
                <div style="display:flex;align-items:center;gap:12px">
                    @if($item->product_image)
                    <img src="{{ asset('storage/'.$item->product_image) }}" style="width:44px;height:44px;border-radius:6px;object-fit:cover;border:1px solid var(--border)">
                    @endif
                    <div>
                        <div style="font-weight:600;font-size:.88rem">{{ $item->product_name_ar }}</div>
                        @if($item->variant_name)<div style="font-size:.76rem;color:var(--text-muted)">{{ $item->variant_name }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="padding:14px 16px;font-size:.88rem">{{ number_format($item->unit_price,2) }} ر.س</td>
            <td style="padding:14px 16px;font-size:.88rem">{{ $item->quantity }}</td>
            <td style="padding:14px 16px;font-weight:700;font-size:.9rem">{{ number_format($item->total_price,2) }} ر.س</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot style="background:var(--surface)">
            <tr><td colspan="3" style="padding:10px 16px;text-align:end;font-size:.88rem;color:var(--text-muted)">المجموع الفرعي</td><td style="padding:10px 16px;font-weight:600;font-size:.88rem">{{ number_format($order->subtotal,2) }} ر.س</td></tr>
            @if($order->coupon_discount > 0)
            <tr><td colspan="3" style="padding:8px 16px;text-align:end;font-size:.88rem;color:var(--success)">خصم ({{ $order->coupon_code }})</td><td style="padding:8px 16px;font-weight:600;color:var(--success)">- {{ number_format($order->coupon_discount,2) }} ر.س</td></tr>
            @endif
            @if($order->shipping_cost > 0)
            <tr><td colspan="3" style="padding:8px 16px;text-align:end;font-size:.88rem;color:var(--text-muted)">الشحن</td><td style="padding:8px 16px;font-weight:600">{{ number_format($order->shipping_cost,2) }} ر.س</td></tr>
            @endif
            @if($order->tax_amount > 0)
            <tr><td colspan="3" style="padding:8px 16px;text-align:end;font-size:.88rem;color:var(--text-muted)">الضريبة</td><td style="padding:8px 16px;font-weight:600">{{ number_format($order->tax_amount,2) }} ر.س</td></tr>
            @endif
            <tr style="background:var(--primary-light)"><td colspan="3" style="padding:14px 16px;text-align:end;font-weight:800;font-size:1rem">الإجمالي</td><td style="padding:14px 16px;font-weight:800;font-size:1rem;color:var(--primary)">{{ number_format($order->total_amount,2) }} ر.س</td></tr>
        </tfoot>
    </table>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    @if($order->shipping_address)
    @php $addr = $order->shipping_address; @endphp
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px">
        <div style="font-weight:700;font-size:.88rem;margin-bottom:12px"><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-inline-end:6px"></i> عنوان التوصيل</div>
        <div style="font-size:.85rem;line-height:1.8;color:var(--text-muted)">
            <div style="font-weight:600;color:var(--text)">{{ $addr['name'] ?? '' }}</div>
            <div>{{ $addr['phone'] ?? '' }}</div>
            <div>{{ $addr['address_line1'] ?? '' }}</div>
            <div>{{ $addr['city'] ?? '' }}</div>
        </div>
    </div>
    @endif
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px">
        <div style="font-weight:700;font-size:.88rem;margin-bottom:12px"><i class="fas fa-info-circle" style="color:var(--primary);margin-inline-end:6px"></i> تفاصيل الطلب</div>
        <div style="font-size:.85rem;display:flex;flex-direction:column;gap:8px;color:var(--text-muted)">
            <div><strong style="color:var(--text)">رقم الطلب:</strong> #{{ $order->order_number }}</div>
            <div><strong style="color:var(--text)">التاريخ:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div><strong style="color:var(--text)">طريقة الدفع:</strong> {{ $order->payment_method === 'cod' ? 'الدفع عند الاستلام' : 'دفع إلكتروني' }}</div>
            <div><strong style="color:var(--text)">حالة الدفع:</strong> <span class="badge badge-{{ $order->payment_status_color }}" style="font-size:.76rem">{{ $order->payment_status_label }}</span></div>
        </div>
    </div>
</div>

<div style="margin-top:16px">
    <a href="{{ route('store.account.orders') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-right"></i> العودة للطلبات
    </a>
</div>
@endsection

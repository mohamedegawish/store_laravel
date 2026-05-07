<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; font-size: 13px; color: #1E293B; direction: rtl; }
        .page { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 20px; border-bottom: 2px solid #6C3FC5; }
        .logo-area h1 { font-size: 22px; font-weight: 800; color: #6C3FC5; }
        .logo-area p { font-size: 11px; color: #64748B; margin-top: 4px; }
        .invoice-info { text-align: left; }
        .invoice-info h2 { font-size: 18px; color: #6C3FC5; margin-bottom: 8px; }
        .invoice-info p { font-size: 11px; color: #64748B; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .info-box h4 { font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
        .info-box p { font-size: 12px; line-height: 1.7; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #F1F5F9; padding: 10px 14px; font-size: 11px; font-weight: 700; color: #64748B; text-align: right; border-bottom: 1px solid #E2E8F0; }
        td { padding: 10px 14px; border-bottom: 1px solid #E2E8F0; font-size: 12px; }
        .total-row { font-weight: 800; font-size: 15px; background: #EEF2FF; }
        .total-row td { color: #6C3FC5; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
        .badge-green { background: #D1FAE5; color: #065F46; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #E2E8F0; text-align: center; font-size: 11px; color: #94A3B8; }
    </style>
</head>
<body>
<div class="page">
    {{-- Header --}}
    <div class="header">
        <div class="logo-area">
            @if($settings?->logo)
            <img src="{{ public_path('storage/'.$settings->logo) }}" alt="logo" style="height:50px;margin-bottom:8px">
            @endif
            <h1>{{ $settings?->store_name_ar ?? config('app.name') }}</h1>
            @if($settings?->phone)<p>{{ $settings->phone }}</p>@endif
            @if($settings?->email)<p>{{ $settings->email }}</p>@endif
        </div>
        <div class="invoice-info">
            <h2>فاتورة ضريبية</h2>
            <p>رقم: <strong>#{{ $order->order_number }}</strong></p>
            <p>التاريخ: {{ $order->created_at->format('d/m/Y') }}</p>
            <p style="margin-top:8px">
                <span class="badge badge-{{ in_array($order->payment_status,['paid']) ? 'green' : 'yellow' }}">
                    {{ $order->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                </span>
            </p>
        </div>
    </div>

    {{-- Client & Shipping --}}
    <div class="info-grid">
        <div class="info-box">
            <h4>بيانات العميل</h4>
            <p><strong>{{ $order->user?->name ?? 'عميل' }}</strong><br>
            {{ $order->user?->email }}<br>
            {{ $order->user?->phone }}</p>
        </div>
        <div class="info-box">
            <h4>عنوان التوصيل</h4>
            @if($order->shipping_address)
            @php $addr = $order->shipping_address @endphp
            <p>{{ $addr['name'] ?? '' }}<br>
            {{ $addr['phone'] ?? '' }}<br>
            {{ $addr['address_line1'] ?? '' }}<br>
            {{ $addr['city'] ?? '' }}</p>
            @endif
        </div>
    </div>

    {{-- Items --}}
    <table>
        <thead>
            <tr><th>#</th><th>المنتج</th><th>السعر</th><th>الكمية</th><th>الإجمالي</th></tr>
        </thead>
        <tbody>
        @foreach($order->orderItems as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>
                {{ $item->product_name_ar }}
                @if($item->variant_name)<br><small style="color:#64748B">{{ $item->variant_name }}</small>@endif
            </td>
            <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
            <td>{{ $item->quantity }}</td>
            <td><strong>{{ number_format($item->total_price, 2) }} ر.س</strong></td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="4" style="text-align:right">المجموع الفرعي</td><td>{{ number_format($order->subtotal, 2) }} ر.س</td></tr>
            @if($order->coupon_discount > 0)
            <tr><td colspan="4" style="text-align:right;color:#EF4444">خصم ({{ $order->coupon_code }})</td><td style="color:#EF4444">- {{ number_format($order->coupon_discount, 2) }} ر.س</td></tr>
            @endif
            @if($order->shipping_cost > 0)
            <tr><td colspan="4" style="text-align:right">الشحن</td><td>{{ number_format($order->shipping_cost, 2) }} ر.س</td></tr>
            @endif
            @if($order->tax_amount > 0)
            <tr><td colspan="4" style="text-align:right">ضريبة القيمة المضافة</td><td>{{ number_format($order->tax_amount, 2) }} ر.س</td></tr>
            @endif
            <tr class="total-row"><td colspan="4" style="text-align:right">الإجمالي</td><td>{{ number_format($order->total_amount, 2) }} ر.س</td></tr>
        </tfoot>
    </table>

    <div class="footer">
        شكراً لتسوقكم معنا — {{ $settings?->store_name_ar ?? config('app.name') }}
        @if($settings?->footer_text_ar)<br>{{ $settings->footer_text_ar }}@endif
    </div>
</div>
</body>
</html>

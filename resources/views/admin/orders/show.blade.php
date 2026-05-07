@extends('layouts.admin')
@section('title', 'تفاصيل الطلب #' . $order->order_number)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.orders.index') }}">الطلبات</a><span class="sep">/</span>
    <span class="current">#{{ $order->order_number }}</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">طلب #{{ $order->order_number }}</h1>
        <p class="page-subtitle">{{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-outline"><i class="fas fa-file-pdf"></i> طباعة الفاتورة</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">

{{-- ── Left ── --}}
<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Order Items --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-shopping-cart" style="color:var(--primary)"></i> المنتجات المطلوبة</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>المنتج</th><th>السعر</th><th>الكمية</th><th>الإجمالي</th></tr></thead>
                <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : 'https://via.placeholder.com/44' }}"
                                 style="width:44px;height:44px;border-radius:8px;object-fit:cover;flex-shrink:0" alt="">
                            <div>
                                <div style="font-weight:600;font-size:.88rem">{{ $item->product_name_ar }}</div>
                                @if($item->variant_name)<div style="font-size:.75rem;color:var(--text-muted)">{{ $item->variant_name }}</div>@endif
                                @if($item->sku)<div style="font-size:.72rem;color:var(--text-muted)">SKU: {{ $item->sku }}</div>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="font-weight:700">{{ number_format($item->total_price, 2) }} ر.س</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--surface-2)">
                        <td colspan="3" style="text-align:end;font-weight:600">المجموع الفرعي</td>
                        <td style="font-weight:700">{{ number_format($order->subtotal, 2) }} ر.س</td>
                    </tr>
                    @if($order->coupon_discount > 0)
                    <tr><td colspan="3" style="text-align:end">خصم الكوبون ({{ $order->coupon_code }})</td><td style="color:var(--danger)">- {{ number_format($order->coupon_discount, 2) }} ر.س</td></tr>
                    @endif
                    @if($order->shipping_cost > 0)
                    <tr><td colspan="3" style="text-align:end">الشحن</td><td>{{ number_format($order->shipping_cost, 2) }} ر.س</td></tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr><td colspan="3" style="text-align:end">الضريبة</td><td>{{ number_format($order->tax_amount, 2) }} ر.س</td></tr>
                    @endif
                    <tr style="background:var(--primary-soft)">
                        <td colspan="3" style="text-align:end;font-weight:800;font-size:1rem">الإجمالي</td>
                        <td style="font-weight:800;font-size:1rem;color:var(--primary)">{{ number_format($order->total_amount, 2) }} ر.س</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Notes --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-note-sticky" style="color:var(--warning)"></i> ملاحظات</span></div>
        <div class="card-body">
            @if($order->notes)
            <div style="background:var(--surface-2);border-radius:8px;padding:12px;font-size:.88rem;margin-bottom:12px">
                <span style="font-weight:600">ملاحظات العميل: </span>{{ $order->notes }}
            </div>
            @endif
            <form method="POST" action="{{ route('admin.orders.update-notes', $order) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">ملاحظات الإدارة</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="أضف ملاحظة داخلية...">{{ $order->admin_notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-save"></i> حفظ الملاحظات</button>
            </form>
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-timeline" style="color:var(--info)"></i> سجل الحالات</span></div>
        <div class="card-body">
            @forelse($order->statusHistory as $history)
            <div style="display:flex;gap:12px;margin-bottom:14px">
                <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:6px;flex-shrink:0"></div>
                <div>
                    <div style="font-size:.88rem;font-weight:600">
                        @if($history->from_status)
                            <span class="badge badge-secondary" style="font-size:.72rem">{{ \App\Models\Order::$statuses[$history->from_status]['label_ar'] ?? $history->from_status }}</span>
                            <i class="fas fa-arrow-left" style="font-size:.7rem;color:var(--text-muted)"></i>
                        @endif
                        <span class="badge badge-primary" style="font-size:.72rem">{{ \App\Models\Order::$statuses[$history->to_status]['label_ar'] ?? $history->to_status }}</span>
                    </div>
                    @if($history->notes)<div style="font-size:.82rem;color:var(--text-muted);margin-top:3px">{{ $history->notes }}</div>@endif
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px">{{ $history->created_at->format('d/m/Y H:i') }} — {{ $history->createdBy?->name ?? 'النظام' }}</div>
                </div>
            </div>
            @empty
            <p style="color:var(--text-muted);font-size:.85rem">لا يوجد سجل</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Right ── --}}
<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Status Update --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-sync" style="color:var(--accent)"></i> تحديث الحالة</span></div>
        <div class="card-body">
            <div style="margin-bottom:16px">
                <span class="badge badge-{{ $order->status_color }}" style="font-size:.9rem;padding:6px 16px">{{ $order->status_label }}</span>
            </div>
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">الحالة الجديدة</label>
                    <select name="status" class="form-control form-select">
                        @foreach($statuses as $key => $info)
                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $info['label_ar'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">ملاحظة</label>
                    <input type="text" name="notes" class="form-control" placeholder="سبب التغيير...">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%"><i class="fas fa-check"></i> تحديث</button>
            </form>
        </div>
    </div>

    {{-- Payment Status --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-credit-card" style="color:var(--info)"></i> حالة الدفع</span></div>
        <div class="card-body">
            <div style="margin-bottom:12px">
                <span class="badge badge-{{ $order->payment_status_color }}" style="font-size:.88rem;padding:5px 14px">{{ $order->payment_status_label }}</span>
                <div style="font-size:.82rem;color:var(--text-muted);margin-top:6px">طريقة الدفع: {{ $order->payment_method === 'cod' ? 'الدفع عند الاستلام' : 'دفع إلكتروني' }}</div>
            </div>
            <form method="POST" action="{{ route('admin.orders.update-payment-status', $order) }}">
                @csrf
                <div class="form-group">
                    <select name="payment_status" class="form-control form-select">
                        @foreach($paymentStatuses as $key => $info)
                        <option value="{{ $key }}" {{ $order->payment_status === $key ? 'selected' : '' }}>{{ $info['label_ar'] }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-outline" style="width:100%">تحديث الدفع</button>
            </form>
        </div>
    </div>

    {{-- Customer --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:var(--primary)"></i> العميل</span></div>
        <div class="card-body">
            @if($order->user)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <img src="{{ $order->user->avatar_url }}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                <div>
                    <div style="font-weight:700">{{ $order->user->name }}</div>
                    <div style="font-size:.82rem;color:var(--text-muted)">{{ $order->user->email }}</div>
                </div>
            </div>
            <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-outline btn-sm" style="width:100%">عرض الملف الشخصي</a>
            @else
            <p style="color:var(--text-muted);font-size:.88rem">عميل مجهول</p>
            @endif
        </div>
    </div>

    {{-- Shipping Address --}}
    @if($order->shipping_address)
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-map-marker-alt" style="color:var(--danger)"></i> عنوان التوصيل</span></div>
        <div class="card-body" style="font-size:.88rem;line-height:1.8">
            @php $addr = $order->shipping_address @endphp
            <div style="font-weight:700">{{ $addr['name'] ?? '' }}</div>
            <div>{{ $addr['phone'] ?? '' }}</div>
            <div>{{ $addr['address_line1'] ?? '' }}</div>
            @if(!empty($addr['address_line2']))<div>{{ $addr['address_line2'] }}</div>@endif
            <div>{{ $addr['city'] ?? '' }}{{ !empty($addr['state']) ? '، '.$addr['state'] : '' }}</div>
        </div>
    </div>
    @endif
</div>

</div>{{-- end grid --}}
@endsection

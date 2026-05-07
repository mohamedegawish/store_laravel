@extends('layouts.company')

@section('title', "الطلب #{$order->id} | إدارة المتجر")

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('company.orders.index') }}">الطلبات</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">الطلب #{{ $order->id }}</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الطلب #{{ $order->id }}</h1>
        <p class="page-subtitle">{{ $order->created_at->translatedFormat('d F Y - h:i A') }}</p>
    </div>
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> طباعة الفاتورة</button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    
    {{-- ── MAIN COL ── --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Items --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">المنتجات المطلوبة</h3></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>سعر الوحدة</th>
                            <th>الكمية</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:40px;height:40px;border-radius:6px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                                        @if($item->product && count($item->product->images ?? []))
                                            <img src="{{ asset('storage/'.$item->product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <i class="fas fa-image" style="color:var(--text-3);"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:600; font-size:0.9rem;">{{ $item->product?->name ?? 'منتج محذوف' }}</div>
                                        @if($item->productVariant?->sku)
                                            <div style="font-size:0.75rem; color:var(--text-3); font-family:monospace;">SKU: {{ $item->productVariant->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--text-2);">{{ number_format($item->unit_price, 2) }} ج.م</td>
                            <td style="font-weight:700;">× {{ $item->quantity }}</td>
                            <td style="font-weight:700; color:var(--primary);">{{ number_format($item->unit_price * $item->quantity, 2) }} ج.م</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:20px; background:var(--surface-2); display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                <div style="display:flex; width:250px; justify-content:space-between; font-size:0.9rem; color:var(--text-2);">
                    <span>المجموع الفرعي:</span>
                    <span style="font-weight:600; color:var(--text);">{{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
                <div style="display:flex; width:250px; justify-content:space-between; font-size:0.9rem; color:var(--text-2);">
                    <span>رسوم الشحن والتوصيل:</span>
                    <span style="font-weight:600; color:var(--success);">مجاناً</span>
                </div>
                <div style="width:250px; height:1px; background:var(--border); margin:5px 0;"></div>
                <div style="display:flex; width:250px; justify-content:space-between; font-size:1.2rem; font-weight:800;">
                    <span>الإجمالي:</span>
                    <span style="color:var(--primary);">{{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── SIDE COL ── --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Order Status Update --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">تحديث حالة الطلب</h3></div>
            <div class="card-body">
                <form action="{{ route('company.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">حالة الطلب</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>مؤكد وجاهز</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>مكتمل التوصيل</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>تمت الإلغاء</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top:15px;">
                        <label class="form-label">حالة الدفع</label>
                        <select name="payment_status" class="form-control">
                            <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>انتظار (الدفع عند الاستلام)</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>مرفوض / فشل</option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>مسترد (Refunded)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:20px;">
                        <i class="fas fa-save"></i> حفظ التحديثات
                    </button>
                </form>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-user-circle"></i> معلومات العميل</h3></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;font-size:1.1rem;flex-shrink:0;">
                        {{ mb_substr($order->user?->name ?? 'ز', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight:700;">{{ $order->user?->name ?? 'زائر' }}</div>
                        <div style="font-size:0.8rem; color:var(--text-3);">عميل بالتجزئة</div>
                    </div>
                </div>
                
                @if($order->user?->email)
                <div style="display:flex; gap:10px; font-size:0.9rem;">
                    <i class="fas fa-envelope" style="color:var(--text-3); margin-top:3px;"></i>
                    <a href="mailto:{{ $order->user->email }}" style="color:var(--primary);">{{ $order->user->email }}</a>
                </div>
                @endif
                
                @if($order->user?->phone)
                <div style="display:flex; gap:10px; font-size:0.9rem;">
                    <i class="fas fa-phone" style="color:var(--text-3); margin-top:3px;"></i>
                    <a href="tel:{{ $order->user->phone }}" style="color:var(--text-2);" dir="ltr">{{ $order->user->phone }}</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-truck"></i> تفاصيل التوصيل</h3></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                @php $addr = $order->shipping_address ?? []; @endphp
                
                <div style="display:flex; gap:10px; font-size:0.9rem;">
                    <i class="fas fa-location-dot" style="color:var(--text-3); margin-top:3px;"></i>
                    <div style="color:var(--text-2); line-height:1.6;">
                        <strong>المدينة:</strong> {{ $addr['city'] ?? 'غير محدد' }}<br>
                        <strong>العنوان الدقيق:</strong> {{ $addr['address'] ?? 'غير محدد' }}
                    </div>
                </div>
                
                <div style="display:flex; gap:10px; font-size:0.9rem;">
                    <i class="fas fa-mobile-screen" style="color:var(--text-3); margin-top:3px;"></i>
                    <div style="color:var(--text-2);">
                        <strong>جوال الاستلام:</strong> <span dir="ltr">{{ $addr['phone'] ?? 'غير محدد' }}</span>
                    </div>
                </div>

                @if(!empty($order->notes))
                <div style="background:var(--warning-soft); padding:12px; border-radius:var(--radius-md); margin-top:10px; color:var(--warning);">
                    <div style="font-weight:700; font-size:0.8rem; margin-bottom:4px;"><i class="fas fa-sticky-note"></i> ملاحظات العميل:</div>
                    <div style="font-size:0.9rem; line-height:1.5;">{{ $order->notes }}</div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

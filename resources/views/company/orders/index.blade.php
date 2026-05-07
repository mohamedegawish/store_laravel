@extends('layouts.company')

@section('title', 'الطلبات | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">الطلبات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إدارة الطلبات 📦</h1>
        <p class="page-subtitle">تابع وعالج طلبات عملائك من هنا.</p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-direction: column; align-items: stretch; gap: 16px;">
        <h3 class="card-title">قائمة الطلبات</h3>
        
        <div class="filter-bar" style="flex-wrap: wrap;">
            <form action="{{ route('company.orders.index') }}" method="GET" style="display:flex; gap:10px; flex:1; flex-wrap:wrap;">
                
                <div class="search-wrap" style="flex:1; min-width:200px;">
                    <input type="text" name="search" class="form-control" placeholder="ابحث برقم الطلب أو اسم العميل..." value="{{ request('search') }}">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>مشحون</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>

                <select name="payment_status" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">حالة الدفع</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>انتظار الدفع</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                </select>

                @if(request()->hasAny(['search','status','payment_status']))
                    <a href="{{ route('company.orders.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> مسح</a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                    <th>الدفع</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-weight:700; font-family:monospace; font-size:0.95rem;">#{{ $order->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                {{ mb_substr($order->user?->name ?? 'ز', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:0.9rem;">{{ $order->user?->name ?? 'زائر' }}</div>
                                <div style="font-size:0.75rem;color:var(--text-3);">{{ $order->user?->phone ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:0.85rem; color:var(--text-2);">
                        {{ $order->created_at->format('Y/m/d') }}<br>
                        <span style="color:var(--text-3);">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td style="font-weight:700; color:var(--accent);">{{ number_format($order->total_amount, 2) }} ج.م</td>
                    <td>
                        @php
                            $pColors = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'secondary'];
                            $pLabels = ['pending'=>'انتظار','paid'=>'مدفوع','failed'=>'مرفوض','refunded'=>'مسترد'];
                        @endphp
                        <span class="badge badge-{{ $pColors[$order->payment_status ?? 'pending'] ?? 'secondary' }}">
                            {{ $pLabels[$order->payment_status ?? 'pending'] ?? ($order->payment_status ?? '—') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $oColors = ['pending'=>'warning','confirmed'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $oLabels = ['pending'=>'قيد المراجعة','confirmed'=>'مؤكد','shipped'=>'مشحون','delivered'=>'مكتمل','cancelled'=>'ملغي'];
                        @endphp
                        <span class="badge badge-{{ $oColors[$order->status] ?? 'secondary' }}">
                            {{ $oLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('company.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> التفاصيل
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>لا توجد طلبات هنا</h3>
                            <p>لم يتم العثور على طلبات تطابق معايير البحث.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection

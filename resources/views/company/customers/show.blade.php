@extends('layouts.company')

@section('title', "ملف العميل: {$user->name} | إدارة المتجر")

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('company.customers.index') }}">العملاء</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">ملف العميل</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الملف الشخصي للعميل</h1>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
    
    {{-- ── CUSTOMER CARD ── --}}
    <div class="card" style="position: sticky; top: 90px;">
        <div class="card-body" style="text-align:center; padding:30px 20px;">
            <div style="width:80px;height:80px;border-radius:50%;background:var(--primary-soft);color:var(--primary);font-size:2.5rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <h2 style="font-weight:800; font-size:1.4rem; margin-bottom:5px;">{{ $user->name }}</h2>
            <div style="color:var(--text-light); font-size:0.9rem; margin-bottom:20px;">تاريخ التسجيل: {{ $user->created_at->format('Y/m/d') }}</div>

            @if($totalSpent > 2000)
                <span class="badge" style="background:#FEF08A; color:#854D0E; font-size:0.9rem; padding:6px 14px;"><i class="fas fa-star"></i> عميل مميز (VIP)</span>
            @endif

            <hr style="border:none; border-top:1px solid var(--border); margin:24px 0;">

            <div style="display:flex; flex-direction:column; gap:16px; text-align:right;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--text-2);"><i class="fas fa-envelope"></i></div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.75rem; color:var(--text-3);">البريد الإلكتروني</div>
                        <a href="mailto:{{ $user->email }}" style="font-weight:600; color:var(--primary); text-overflow:ellipsis; overflow:hidden; white-space:nowrap; display:block;">{{ $user->email }}</a>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--text-2);"><i class="fas fa-phone"></i></div>
                    <div style="flex:1;">
                        <div style="font-size:0.75rem; color:var(--text-3);">رقم الجوال</div>
                        <div style="font-weight:600; font-family:monospace;" dir="ltr">{{ $user->phone ?? 'غير متوفر' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STATS & ORDERS ── --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Stats Grid --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="stat-card primary">
                <div class="stat-header">
                    <span class="stat-label">إجمالي الطلبات لديك</span>
                    <span class="stat-icon primary"><i class="fas fa-shopping-bag"></i></span>
                </div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
            <div class="stat-card accent">
                <div class="stat-header">
                    <span class="stat-label">إجمالي المشتريات (مكتملة)</span>
                    <span class="stat-icon accent"><i class="fas fa-wallet"></i></span>
                </div>
                <div class="stat-value">{{ number_format($totalSpent, 2) }}<small style="font-size:0.4em"> ج.م</small></div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-clock-rotate-left"></i> سجل الطلبات السابقة</h3></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>التاريخ</th>
                            <th>الإجمالي</th>
                            <th>حالة الطلب</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td style="font-weight:700; font-family:monospace;">#{{ $order->id }}</td>
                            <td style="font-size:0.85rem; color:var(--text-2);">{{ $order->created_at->format('Y/m/d - H:i') }}</td>
                            <td style="font-weight:700; color:var(--accent);">{{ number_format($order->total_amount, 2) }} ج.م</td>
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
                                <a href="{{ route('company.orders.show', $order) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:var(--text-light);">
                                <i class="fas fa-box-open" style="font-size:2rem; opacity:0.3; margin-bottom:10px;"></i>
                                <div>لا يوجد طلبات سابقة للعميل.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

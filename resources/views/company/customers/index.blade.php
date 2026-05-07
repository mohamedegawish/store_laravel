@extends('layouts.company')

@section('title', 'قائمة العملاء | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">العملاء</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">قاعدة العملاء 👥</h1>
        <p class="page-subtitle">شاهد بيانات عملائك وسجل مشترياتهم لديك.</p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-direction: column; align-items: stretch; gap: 16px;">
        <h3 class="card-title">قائمة العملاء</h3>
        
        <div class="filter-bar">
            <form action="{{ route('company.customers.index') }}" method="GET" class="search-wrap" style="flex:1;">
                <input type="text" name="search" class="form-control" placeholder="ابحث باسم العميل أو الإيميل أو الجوال..." value="{{ request('search') }}">
                <i class="fas fa-search search-icon"></i>
            </form>
            @if(request('search'))
                <a href="{{ route('company.customers.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> مسح</a>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجمالي الطلبات</th>
                    <th>إجمالي المشتريات (مكتملة)</th>
                    <th>تصنيف العميل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;font-size:1.1rem;flex-shrink:0;">
                                {{ mb_substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:0.95rem;">{{ $customer->name }}</div>
                                <div style="font-size:0.75rem; color:var(--text-3); font-family:monospace;">{{ $customer->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-2); font-size:0.85rem;">{{ $customer->created_at->format('Y/m/d') }}</td>
                    <td style="font-weight:700;">{{ $customer->total_orders }} طلب</td>
                    <td style="font-weight:800; color:var(--accent);">{{ number_format($customer->total_spent ?? 0, 2) }} ج.م</td>
                    <td>
                        @if(($customer->total_spent ?? 0) > 2000)
                            <span class="badge" style="background:#FEF08A; color:#854D0E;"><i class="fas fa-star"></i> عميل مميز (VIP)</span>
                        @elseif(($customer->total_spent ?? 0) > 500)
                            <span class="badge" style="background:#E0F2FE; color:#075985;">عميل نشط</span>
                        @else
                            <span class="badge badge-secondary">عميل عادي</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('company.customers.show', $customer) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user"></i> الملف الشخصي
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>لا يوجد عملاء حتى الآن</h3>
                            <p>سيتم استعراض العملاء هنا تلقائياً بمجرد قيامهم بالشراء من متجرك.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>

@endsection

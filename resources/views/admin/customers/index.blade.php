@extends('layouts.admin')
@section('title', 'العملاء')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">العملاء</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">العملاء</h1><p class="page-subtitle">{{ $customers->total() }} عميل</p></div>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:2">
        <label>بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="الاسم، البريد، الهاتف...">
    </div>
    <div style="display:flex;gap:8px;align-items:flex-end">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">مسح</a>
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>العميل</th><th>الهاتف</th><th>الطلبات</th><th>إجمالي الإنفاق</th><th>تاريخ التسجيل</th><th>الحالة</th><th>إجراءات</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <img src="{{ $customer->avatar_url }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
                        <div>
                            <div style="font-weight:600;font-size:.88rem">{{ $customer->name }}</div>
                            <div style="font-size:.76rem;color:var(--text-muted)">{{ $customer->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.85rem">{{ $customer->phone ?? '—' }}</td>
                <td>{{ $customer->orders_count ?? 0 }}</td>
                <td style="font-weight:600">{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }} ر.س</td>
                <td style="font-size:.82rem;color:var(--text-muted)">{{ $customer->created_at->format('d/m/Y') }}</td>
                <td><span class="badge badge-{{ $customer->is_active ?? true ? 'success' : 'secondary' }}">{{ ($customer->is_active ?? true) ? 'نشط' : 'محظور' }}</span></td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-xs btn-outline"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">لا يوجد عملاء</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">
        <span>إجمالي: {{ $customers->total() }}</span>
        {{ $customers->links() }}
    </div>
</div>
@endsection

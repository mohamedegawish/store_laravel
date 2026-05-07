@extends('layouts.admin')
@section('title', 'الطلبات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الطلبات</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">الطلبات</h1>
        <p class="page-subtitle">{{ $orders->total() }} طلب</p>
    </div>
    <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline"><i class="fas fa-chart-bar"></i> تقرير المبيعات</a>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:2">
        <label>بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="رقم الطلب، اسم العميل...">
    </div>
    <div class="filter-group">
        <label>الحالة</label>
        <select name="status" class="form-control form-select">
            <option value="">الكل</option>
            @foreach($statuses as $key => $info)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $info['label_ar'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>حالة الدفع</label>
        <select name="payment_status" class="form-control form-select">
            <option value="">الكل</option>
            @foreach($paymentStatuses as $key => $info)
            <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>{{ $info['label_ar'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>من تاريخ</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>
    <div class="filter-group">
        <label>إلى تاريخ</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
    </div>
    <div style="display:flex;gap:8px;align-items:flex-end">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline">مسح</a>
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>رقم الطلب</th><th>العميل</th><th>المنتجات</th><th>الإجمالي</th>
                <th>الدفع</th><th>الحالة</th><th>التاريخ</th><th>إجراءات</th>
            </tr></thead>
            <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" style="font-weight:700;color:var(--primary)">#{{ $order->order_number }}</a>
                </td>
                <td>
                    <div style="font-weight:600;font-size:.88rem">{{ $order->user?->name ?? 'زائر' }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted)">{{ $order->user?->phone }}</div>
                </td>
                <td style="font-size:.85rem;color:var(--text-muted)">{{ $order->orderItems->count() }} منتج</td>
                <td style="font-weight:700">{{ number_format($order->total_amount, 2) }} ر.س</td>
                <td><span class="badge badge-{{ $order->payment_status_color }}">{{ $order->payment_status_label }}</span></td>
                <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                <td style="font-size:.82rem;color:var(--text-muted)">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-xs btn-outline" title="فاتورة"><i class="fas fa-file-pdf"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">لا توجد طلبات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">
        <span>إجمالي: {{ $orders->total() }} طلب</span>
        {{ $orders->links() }}
    </div>
</div>
@endsection

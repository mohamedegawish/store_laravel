@extends('layouts.admin')
@section('title', 'ملف العميل')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.customers.index') }}">العملاء</a><span class="sep">/</span>
    <span class="current">{{ $customer->name }}</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">{{ $customer->name }}</h1></div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start">

    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-body" style="text-align:center">
                <img src="{{ $customer->avatar_url }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 12px">
                <div style="font-weight:800;font-size:1.1rem">{{ $customer->name }}</div>
                <div style="font-size:.85rem;color:var(--text-muted)">{{ $customer->email }}</div>
                @if($customer->phone)<div style="font-size:.85rem;color:var(--text-muted)">{{ $customer->phone }}</div>@endif
                <div style="margin-top:12px">
                    <span class="badge badge-{{ ($customer->is_active ?? true) ? 'success' : 'secondary' }}">{{ ($customer->is_active ?? true) ? 'نشط' : 'محظور' }}</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">إحصائيات</span></div>
            <div class="card-body">
                @foreach([['إجمالي الطلبات', $customer->orders->count()],['مكتملة', $customer->orders->where('status','delivered')->count()],['ملغاة', $customer->orders->where('status','cancelled')->count()],['إجمالي الإنفاق', number_format($customer->orders->sum('total_amount'),2).' ر.س']] as [$label,$val])
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.88rem">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span style="font-weight:700">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">آخر الطلبات</span></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>رقم الطلب</th><th>الإجمالي</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                    <tbody>
                    @forelse($customer->orders()->latest()->limit(10)->get() as $order)
                    <tr>
                        <td style="font-weight:700">#{{ $order->order_number }}</td>
                        <td>{{ number_format($order->total_amount,2) }} ر.س</td>
                        <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                        <td style="font-size:.82rem;color:var(--text-muted)">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--text-muted)">لا توجد طلبات</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

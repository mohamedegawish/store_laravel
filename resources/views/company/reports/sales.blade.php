@extends('layouts.company')

@section('title', 'تقرير المبيعات | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تقرير المبيعات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تقرير المبيعات</h1>
        <p class="page-subtitle">تحليل أداء المبيعات والإيرادات والأرباح</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> طباعة</button>
</div>

{{-- Date Filters --}}
<div class="filter-bar">
    <form action="{{ route('company.reports.sales') }}" method="GET" style="display:flex; gap:10px; flex-wrap:wrap; width:100%; align-items:center;">
        <div class="form-group" style="margin:0; display:flex; align-items:center; gap:8px;">
            <label class="form-label" style="margin:0; white-space:nowrap; color:var(--text-2);">من:</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}" style="width:160px;">
        </div>
        <div class="form-group" style="margin:0; display:flex; align-items:center; gap:8px;">
            <label class="form-label" style="margin:0; white-space:nowrap; color:var(--text-2);">إلى:</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}" style="width:160px;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> تطبيق</button>
        <a href="{{ route('company.reports.sales') }}" class="btn btn-secondary">إعادة تعيين</a>
    </form>
</div>

{{-- Summary Stats --}}
<div class="stats-grid" style="margin-top:20px; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
    <div class="stat-card accent">
        <div class="stat-header">
            <span class="stat-label">إجمالي الإيرادات</span>
            <span class="stat-icon accent"><i class="fas fa-wallet"></i></span>
        </div>
        <div class="stat-value">{{ number_format($totalRevenue, 2) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend">خلال الفترة المختارة</div>
    </div>
    <div class="stat-card success">
        <div class="stat-header">
            <span class="stat-label">صافي الأرباح</span>
            <span class="stat-icon success"><i class="fas fa-chart-line"></i></span>
        </div>
        <div class="stat-value">{{ number_format($totalProfit, 2) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend">بعد خصم التكاليف</div>
    </div>
    <div class="stat-card primary">
        <div class="stat-header">
            <span class="stat-label">عدد الطلبات</span>
            <span class="stat-icon primary"><i class="fas fa-cart-shopping"></i></span>
        </div>
        <div class="stat-value">{{ $totalOrders }}</div>
        <div class="stat-trend">طلب مكتمل</div>
    </div>
    <div class="stat-card info">
        <div class="stat-header">
            <span class="stat-label">متوسط قيمة الطلب</span>
            <span class="stat-icon info"><i class="fas fa-receipt"></i></span>
        </div>
        <div class="stat-value">{{ number_format($avgOrderValue, 2) }}<small style="font-size:0.4em"> ج.م</small></div>
        <div class="stat-trend">متوسط لكل طلب</div>
    </div>
</div>

{{-- Annual Chart --}}
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-bar"></i> المبيعات الشهرية ({{ now()->year }})</h3>
    </div>
    <div class="card-body">
        <canvas id="salesReportChart" height="100"></canvas>
    </div>
</div>

{{-- Orders Table --}}
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table"></i> الطلبات المكتملة في الفترة المحددة</h3>
        <span class="badge badge-success">{{ $orders->total() }} طلب</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-weight:700; font-family:monospace;">#{{ $order->id }}</td>
                    <td>{{ $order->user?->name ?? 'زائر' }}</td>
                    <td style="color:var(--text-2); font-size:0.875rem;">{{ $order->created_at->format('Y/m/d') }}</td>
                    <td style="font-weight:700; color:var(--accent);">{{ number_format($order->total_amount, 2) }} ج.م</td>
                    <td>
                        <a href="{{ route('company.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>لا توجد طلبات في هذه الفترة</h3>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="card-footer">{{ $orders->appends(request()->query())->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('salesReportChart');
if (ctx) {
    const data = @json($monthlyData);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'],
            datasets: [
                {
                    label: 'الإيرادات (ج.م)',
                    data: data.map(d => d.total),
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderRadius: 5,
                },
                {
                    label: 'عدد الطلبات',
                    data: data.map(d => d.orders_count),
                    backgroundColor: 'rgba(99,102,241,0.65)',
                    borderRadius: 5,
                    yAxisID: 'y2',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'ج.م' } },
                y2: { beginAtZero: true, position: 'left', title: { display: true, text: 'عدد الطلبات' }, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
}
</script>
@endpush

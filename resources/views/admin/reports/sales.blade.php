@extends('layouts.admin')
@section('title', 'تقرير المبيعات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">تقرير المبيعات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">تقرير المبيعات</h1></div>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <select name="period" class="form-control form-select" style="width:160px">
            @foreach(['7d'=>'آخر 7 أيام','30d'=>'آخر 30 يوم','90d'=>'آخر 90 يوم','year'=>'هذا العام'] as $val=>$label)
            <option value="{{ $val }}" {{ request('period','30d') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> تطبيق</button>
    </form>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:28px">
    @foreach([
        ['fas fa-dollar-sign', 'الإيرادات', number_format($totals['revenue'] ?? 0, 2) . ' ر.س', 'primary'],
        ['fas fa-shopping-cart', 'الطلبات', $totals['orders'] ?? 0, 'info'],
        ['fas fa-receipt', 'متوسط الطلب', number_format($totals['avg_order'] ?? 0, 2) . ' ر.س', 'success'],
    ] as [$icon, $label, $val, $color])
    <div class="stat-card">
        <div class="stat-card-icon" style="background:var(--{{ $color }}-soft,var(--primary-soft));color:var(--{{ $color }},var(--primary))"><i class="{{ $icon }}"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-value">{{ $val }}</div>
            <div class="stat-card-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header"><span class="card-title">المبيعات عبر الزمن</span></div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

{{-- Tables --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    {{-- Top Products --}}
    <div class="card">
        <div class="card-header"><span class="card-title">أفضل المنتجات</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>المنتج</th><th>المبيعات</th><th>الإيراد</th></tr></thead>
                <tbody>
                @forelse($topProducts ?? [] as $item)
                <tr>
                    <td style="font-size:.85rem;font-weight:600">{{ $item->product_name_ar }}</td>
                    <td>{{ $item->total_quantity }}</td>
                    <td style="font-weight:700">{{ number_format($item->total_revenue, 2) }} ر.س</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted)">لا بيانات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Orders by Status --}}
    <div class="card">
        <div class="card-header"><span class="card-title">توزيع حالات الطلبات</span></div>
        <div class="card-body">
            <canvas id="statusChart" height="180"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const chartData = @json($chart ?? ['labels'=>[],'revenue'=>[]]);
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: chartData.labels ?? [],
        datasets: [{
            label: 'الإيرادات',
            data: chartData.revenue ?? [],
            borderColor: '#6C3FC5',
            backgroundColor: 'rgba(108,63,197,.1)',
            tension: 0.4,
            fill: true,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

const statusData = @json($byStatus ?? []);
if (statusData.length) {
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.label ?? s.status ?? s),
            datasets: [{ data: statusData.map(s => s.count ?? s.total ?? 0), backgroundColor: ['#6C3FC5','#10B981','#F59E0B','#3B82F6','#EF4444','#8B5CF6'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
}
</script>
@endpush
@endsection

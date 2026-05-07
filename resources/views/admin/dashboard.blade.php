@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('breadcrumb')
    <span class="current">لوحة التحكم</span>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">مرحباً، {{ auth()->user()->name }} 👋</h1>
        <p class="page-subtitle">{{ now()->format('l، d F Y') }} — ملخص المتجر</p>
    </div>
    <div style="display:flex;gap:10px">
        <select id="periodSelect" class="form-control form-select" style="width:auto" onchange="loadChart(this.value)">
            <option value="week">7 أيام</option>
            <option value="month">30 يوم</option>
            <option value="year">سنة</option>
        </select>
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline"><i class="fas fa-chart-bar"></i> التقارير</a>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary)"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">إجمالي الطلبات</div>
            <div class="stat-change change-up"><i class="fas fa-arrow-up"></i> {{ $stats['orders_this_month'] }} هذا الشهر</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--accent-soft);color:var(--accent)"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:1.3rem">{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">إجمالي الإيرادات (ر.س)</div>
            <div class="stat-change change-up"><i class="fas fa-arrow-up"></i> {{ number_format($stats['revenue_this_month'], 0) }} هذا الشهر</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--info-soft);color:var(--info)"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
            <div class="stat-label">إجمالي العملاء</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(139,92,246,.12);color:#8B5CF6"><i class="fas fa-box"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-label">المنتجات النشطة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-soft);color:var(--warning)"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending_orders'] }}</div>
            <div class="stat-label">طلبات معلقة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-soft);color:var(--danger)"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['low_stock_variants'] }}</div>
            <div class="stat-label">منتجات مخزون منخفض</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--accent-soft);color:var(--accent)"><i class="fas fa-sun"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['orders_today'] }}</div>
            <div class="stat-label">طلبات اليوم</div>
            <div class="stat-change change-up">{{ number_format($stats['revenue_today'], 0) }} ر.س</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-soft);color:var(--danger)"><i class="fas fa-ban"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['out_of_stock_variants'] }}</div>
            <div class="stat-label">نفدت المخزون</div>
        </div>
    </div>
</div>

{{-- ── Charts Row ── --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">
    {{-- Sales Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-chart-line" style="color:var(--primary)"></i> المبيعات والإيرادات</span>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height:260px">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Orders by Status --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-chart-pie" style="color:var(--accent)"></i> الطلبات حسب الحالة</span>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height:200px">
                <canvas id="statusChart"></canvas>
            </div>
            <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
                @foreach($ordersByStatus as $status => $count)
                @php $info = \App\Models\Order::$statuses[$status] ?? ['label_ar'=>$status,'color'=>'secondary'] @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:.82rem">
                    <span>{{ $info['label_ar'] }}</span>
                    <span class="badge badge-{{ $info['color'] }}">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Bottom Row ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    {{-- Recent Orders --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-clock-rotate-left" style="color:var(--primary)"></i> آخر الطلبات</span>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline">عرض الكل</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>رقم الطلب</th><th>العميل</th><th>المبلغ</th><th>الحالة</th>
                </tr></thead>
                <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" style="color:var(--primary);font-weight:600">#{{ $order->order_number }}</a></td>
                    <td>{{ $order->user?->name ?? 'زائر' }}</td>
                    <td>{{ number_format($order->total_amount, 0) }} ر.س</td>
                    <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">لا توجد طلبات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products + Low Stock --}}
    <div style="display:flex;flex-direction:column;gap:20px">
        {{-- Top Products --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-fire" style="color:var(--warning)"></i> الأكثر مبيعاً</span>
                <a href="{{ route('admin.reports.products') }}" class="btn btn-sm btn-outline">التفاصيل</a>
            </div>
            <div class="card-body" style="padding:12px 16px">
                @forelse($topProducts as $item)
                <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border)">
                    @php $p = $item->product @endphp
                    <img src="{{ $p?->main_image ? asset('storage/'.$p->main_image) : 'https://via.placeholder.com/40' }}"
                         alt="" style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p?->name_ar ?? '-' }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted)">{{ $item->total_qty }} وحدة</div>
                    </div>
                    <div style="font-size:.85rem;font-weight:700;color:var(--accent)">{{ number_format($item->total_revenue, 0) }} ر.س</div>
                </div>
                @empty
                <p style="text-align:center;color:var(--text-muted);padding:20px 0;font-size:.85rem">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>

        {{-- Low Stock --}}
        @if($lowStock->count() > 0)
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-triangle-exclamation" style="color:var(--danger)"></i> تنبيه المخزون</span>
                <a href="{{ route('admin.reports.inventory') }}" class="btn btn-sm btn-outline">عرض الكل</a>
            </div>
            <div class="card-body" style="padding:12px 16px">
                @foreach($lowStock as $variant)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:7px 0;border-bottom:1px solid var(--border)">
                    <div style="font-size:.83rem;font-weight:600">{{ $variant->product?->name_ar }}</div>
                    <span class="badge {{ $variant->stock_quantity === 0 ? 'badge-danger' : 'badge-warning' }}">
                        {{ $variant->stock_quantity === 0 ? 'نفذ' : $variant->stock_quantity . ' متبقي' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const chartData = @json($salesChart);

function getColors(alpha = 1) {
    const primary = '108,63,197';
    const accent  = '16,185,129';
    return {
        primary: `rgba(${primary},${alpha})`,
        accent:  `rgba(${accent},${alpha})`
    };
}

let salesChartInstance;

function loadChart(period) {
    fetch(`?period=${period}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .catch(() => {});

    const c = getColors;
    const ctx = document.getElementById('salesChart').getContext('2d');
    if (salesChartInstance) salesChartInstance.destroy();
    salesChartInstance = new Chart(ctx, {
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'الطلبات',
                    data: chartData.orders,
                    backgroundColor: 'rgba(108,63,197,0.15)',
                    borderColor: 'rgba(108,63,197,0.7)',
                    borderWidth: 1,
                    borderRadius: 6,
                    yAxisID: 'y1',
                },
                {
                    type: 'line',
                    label: 'الإيرادات (ر.س)',
                    data: chartData.revenue,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    yAxisID: 'y',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', labels: { font: { family: 'Cairo' } } } },
            scales: {
                y: { beginAtZero: true, position: 'right', ticks: { font: { family: 'Cairo' } } },
                y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, ticks: { font: { family: 'Cairo' } } },
                x: { ticks: { font: { family: 'Cairo' } } }
            }
        }
    });
}

// Status donut chart
const statusData = @json($ordersByStatus);
const statusColors = {
    pending: '#F59E0B', confirmed: '#3B82F6', processing: '#8B5CF6',
    shipped: '#6366F1', delivered: '#10B981', cancelled: '#EF4444', refunded: '#94A3B8'
};
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: Object.keys(statusData).map(k => statusColors[k] || '#94A3B8'),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});

loadChart('week');
</script>
@endpush
@endsection

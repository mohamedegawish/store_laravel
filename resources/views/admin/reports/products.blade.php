@extends('layouts.admin')
@section('title', 'تقرير المنتجات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">تقرير المنتجات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">تقرير المنتجات</h1></div>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <select name="period" class="form-control" style="width:160px">
            @foreach(['7d'=>'آخر 7 أيام','30d'=>'آخر 30 يوم','90d'=>'آخر 90 يوم','year'=>'هذا العام'] as $val=>$label)
            <option value="{{ $val }}" {{ request('period','30d') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> تطبيق</button>
    </form>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
    @foreach([
        ['fas fa-box', 'إجمالي المنتجات', $totals['total_products'] ?? 0, 'primary'],
        ['fas fa-check-circle', 'المنتجات النشطة', $totals['active_products'] ?? 0, 'info'],
        ['fas fa-exclamation-triangle', 'منخفض المخزون', $totals['low_stock'] ?? 0, 'warning'],
        ['fas fa-times-circle', 'نفد المخزون', $totals['out_of_stock'] ?? 0, 'danger'],
    ] as [$icon, $label, $val, $color])
    <div class="stat-card {{ $color }}">
        <div class="stat-header">
            <span class="stat-label">{{ $label }}</span>
            <div class="stat-icon {{ $color }}"><i class="{{ $icon }}"></i></div>
        </div>
        <div class="stat-value">{{ number_format($val) }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    {{-- Top selling products --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-trophy"></i> الأكثر مبيعاً</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>المنتج</th><th>الكمية</th><th>الإيراد</th></tr></thead>
                <tbody>
                @forelse($topProducts ?? [] as $i => $item)
                <tr>
                    <td style="color:var(--text-3);font-weight:700">{{ $i+1 }}</td>
                    <td style="font-weight:600">{{ $item->product->name_ar ?? $item->product_name_ar ?? '—' }}</td>
                    <td>{{ $item->total_qty ?? $item->total_quantity ?? 0 }}</td>
                    <td style="font-weight:700;color:var(--accent)">{{ number_format($item->total_revenue, 2) }} ر.س</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-3)">لا بيانات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Products by category --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-chart-pie"></i> توزيع حسب التصنيف</span></div>
        <div class="card-body">
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Low stock alert --}}
@if(($lowStockItems ?? collect())->isNotEmpty())
<div class="card" style="margin-top:24px">
    <div class="card-header"><span class="card-title" style="color:var(--warning)"><i class="fas fa-exclamation-triangle"></i> تنبيه: منتجات منخفضة المخزون</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>المنتج</th><th>SKU</th><th>الكمية الحالية</th><th>إجراء</th></tr></thead>
            <tbody>
            @foreach($lowStockItems as $item)
            <tr>
                <td style="font-weight:600">{{ $item->product->name_ar ?? '—' }}</td>
                <td><code style="font-size:.8rem">{{ $item->sku }}</code></td>
                <td><span class="badge badge-{{ $item->stock_quantity <= 0 ? 'danger' : 'warning' }}">{{ $item->stock_quantity }}</span></td>
                <td><a href="{{ route('admin.products.edit', $item->product_id) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i> تعديل</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const catData = @json($byCategory ?? []);
if (catData.length) {
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.name ?? c.category_name ?? '—'),
            datasets: [{ data: catData.map(c => c.count ?? c.total ?? 0), backgroundColor: ['#6C3FC5','#10B981','#F59E0B','#3B82F6','#EF4444','#8B5CF6','#EC4899'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
}
</script>
@endpush
@endsection

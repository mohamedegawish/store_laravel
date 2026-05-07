@extends('layouts.company')

@section('title', 'أداء المنتجات | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">أداء المنتجات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">أداء المنتجات</h1>
        <p class="page-subtitle">تحليل المنتجات حسب المبيعات والإيرادات والأرباح</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> طباعة</button>
</div>

{{-- Top Products Bar Chart --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-trophy"></i> أفضل 10 منتجات مبيعاً</h3>
    </div>
    <div class="card-body">
        <canvas id="topProductsChart" height="120"></canvas>
    </div>
</div>

{{-- Product Performance Table --}}
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table"></i> تفصيل أداء المنتجات</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المنتج</th>
                    <th>الوحدات المباعة</th>
                    <th>إجمالي الإيرادات</th>
                    <th>صافي الربح</th>
                    <th>مؤشر الأداء</th>
                </tr>
            </thead>
            <tbody>
                @php $maxSold = $topProducts->first()?->total_sold ?? 1; @endphp
                @forelse($topProducts as $i => $product)
                @php
                    $images = json_decode($product->images ?? '[]', true);
                    $perf = $maxSold > 0 ? round(($product->total_sold / $maxSold) * 100) : 0;
                @endphp
                <tr>
                    <td>
                        <span style="display:inline-flex; width:26px; height:26px; align-items:center; justify-content:center;
                            background:{{ $i < 3 ? 'var(--warning-soft)' : 'var(--surface-2)' }};
                            color:{{ $i < 3 ? 'var(--warning)' : 'var(--text-3)' }};
                            border-radius:50%; font-weight:700; font-size:0.8rem;">
                            {{ $topProducts->firstItem() + $i }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:38px;height:38px;border-radius:8px;background:var(--primary-soft);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--primary);">
                                @if(count($images))
                                    <img src="{{ asset('storage/'.$images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="fas fa-image" style="font-size:0.8rem;"></i>
                                @endif
                            </div>
                            <span style="font-weight:600; font-size:0.9rem;">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td><strong style="color:var(--text);">{{ number_format($product->total_sold) }}</strong> <small style="color:var(--text-3);">وحدة</small></td>
                    <td style="font-weight:700; color:var(--accent);">{{ number_format($product->total_revenue, 2) }} ج.م</td>
                    <td>
                        @if($product->total_profit > 0)
                            <span style="font-weight:700; color:var(--success);">{{ number_format($product->total_profit, 2) }} ج.م</span>
                        @else
                            <span style="color:var(--text-3);">غير محدد</span>
                        @endif
                    </td>
                    <td style="min-width:130px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="flex:1; height:6px; background:var(--surface-2); border-radius:3px; overflow:hidden;">
                                <div style="height:100%; width:{{ $perf }}%; background:{{ $i < 3 ? 'var(--warning)' : 'var(--primary)' }}; border-radius:3px;"></div>
                            </div>
                            <span style="font-size:0.75rem; color:var(--text-3); min-width:28px;">{{ $perf }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-chart-bar"></i>
                            <h3>لا توجد بيانات مبيعات بعد</h3>
                            <p>ستظهر إحصائيات المنتجات بعد اكتمال أول طلب.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($topProducts->hasPages())
        <div class="card-footer">{{ $topProducts->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('topProductsChart');
if (ctx) {
    const products = @json($topProducts->take(10)->values());
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: products.map(p => p.name.length > 20 ? p.name.substring(0,20)+'...' : p.name),
            datasets: [{
                label: 'الوحدات المباعة',
                data: products.map(p => p.total_sold),
                backgroundColor: products.map((_, i) => `hsl(${200 + i * 12}, 80%, 55%)`),
                borderRadius: 5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                y: { grid: { display: false } }
            }
        }
    });
}
</script>
@endpush

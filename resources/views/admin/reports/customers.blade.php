@extends('layouts.admin')
@section('title', 'تقرير العملاء')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">تقرير العملاء</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">تقرير العملاء</h1></div>
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
        ['fas fa-users', 'إجمالي العملاء', $totals['total_customers'] ?? 0, 'primary'],
        ['fas fa-user-plus', 'عملاء جدد', $totals['new_customers'] ?? 0, 'info'],
        ['fas fa-repeat', 'عملاء متكررون', $totals['returning_customers'] ?? 0, 'accent'],
        ['fas fa-dollar-sign', 'متوسط قيمة العميل', number_format($totals['avg_ltv'] ?? 0, 2) . ' ر.س', 'warning'],
    ] as [$icon, $label, $val, $color])
    <div class="stat-card {{ $color }}">
        <div class="stat-header">
            <span class="stat-label">{{ $label }}</span>
            <div class="stat-icon {{ $color }}"><i class="{{ $icon }}"></i></div>
        </div>
        <div class="stat-value" style="font-size:1.6rem">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Growth chart --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header"><span class="card-title"><i class="fas fa-chart-line"></i> نمو العملاء</span></div>
    <div class="card-body">
        <canvas id="growthChart" height="100"></canvas>
    </div>
</div>

{{-- Top customers --}}
<div class="card">
    <div class="card-header"><span class="card-title"><i class="fas fa-star"></i> أفضل العملاء</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>العميل</th><th>عدد الطلبات</th><th>إجمالي الإنفاق</th><th>آخر طلب</th><th>إجراء</th></tr></thead>
            <tbody>
            @forelse($topCustomers ?? [] as $i => $customer)
            <tr>
                <td style="color:var(--text-3);font-weight:700">{{ $i+1 }}</td>
                <td>
                    <div style="font-weight:600">{{ $customer->name }}</div>
                    <div style="font-size:.78rem;color:var(--text-3)">{{ $customer->email }}</div>
                </td>
                <td>{{ $customer->orders_count }}</td>
                <td style="font-weight:700;color:var(--accent)">{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }} ر.س</td>
                <td style="font-size:.82rem;color:var(--text-3)">{{ $customer->orders()->latest()->value('created_at') ? \Carbon\Carbon::parse($customer->orders()->latest()->value('created_at'))->diffForHumans() : '—' }}</td>
                <td><a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-xs btn-outline"><i class="fas fa-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-3)">لا بيانات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const growthData = @json($chart ?? ['labels'=>[],'orders'=>[]]);
new Chart(document.getElementById('growthChart'), {
    type: 'bar',
    data: {
        labels: growthData.labels ?? [],
        datasets: [{
            label: 'الطلبات',
            data: growthData.orders ?? [],
            backgroundColor: 'rgba(108,63,197,.7)',
            borderRadius: 4,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
@endpush
@endsection

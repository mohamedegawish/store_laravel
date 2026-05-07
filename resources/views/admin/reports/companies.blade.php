@extends('layouts.admin')

@section('title', 'تقرير الشركات | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تقرير الشركات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تقرير الشركات</h1>
        <p class="page-subtitle">{{ $companies->total() }} نتيجة بناءً على الفلاتر الحالية</p>
    </div>
</div>

{{-- Status doughnut summary --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px;">
    <div class="stat-card accent">
        <div class="stat-header"><span class="stat-label">نشطة</span><span class="stat-icon accent"><i class="fas fa-circle-check"></i></span></div>
        <div class="stat-value">{{ $statusCounts['active'] ?? 0 }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-header"><span class="stat-label">قيد المراجعة</span><span class="stat-icon warning"><i class="fas fa-clock"></i></span></div>
        <div class="stat-value">{{ $statusCounts['pending'] ?? 0 }}</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-header"><span class="stat-label">معطلة</span><span class="stat-icon danger"><i class="fas fa-ban"></i></span></div>
        <div class="stat-value">{{ $statusCounts['inactive'] ?? 0 }}</div>
    </div>
    <div class="card" style="padding:22px; display:flex; align-items:center; justify-content:center;">
        <canvas id="statusPie" style="max-height:120px;"></canvas>
    </div>
</div>

<div class="card">
    {{-- Filter form --}}
    <form method="GET" action="{{ route('admin.reports.companies') }}" id="reportForm">
        <div class="filter-bar">
            <div class="search-wrap">
                <input type="date" name="from" class="form-control" value="{{ request('from') }}" title="من تاريخ">
            </div>
            <div class="search-wrap">
                <input type="date" name="to" class="form-control" value="{{ request('to') }}" title="إلى تاريخ">
            </div>
            <select name="status" class="form-control" onchange="document.getElementById('reportForm').submit()">
                <option value="">جميع الحالات</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>نشطة</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطلة</option>
            </select>
            @if(request()->hasAny(['from','to','status']))
                <a href="{{ route('admin.reports.companies') }}" class="btn btn-secondary btn-sm"><i class="fas fa-rotate"></i> إعادة ضبط</a>
            @endif
            <button type="submit" class="btn btn-primary btn-sm" style="margin-right:auto;"><i class="fas fa-filter"></i> تطبيق</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الشركة</th>
                    <th>المدير</th>
                    <th>البريد الإلكتروني</th>
                    <th>الحالة</th>
                    <th>تاريخ الإضافة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $i => $company)
                <tr>
                    <td style="color:var(--text-3); font-size:0.8rem;">{{ $companies->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;gap:10px;align-items:center;">
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);overflow:hidden;flex-shrink:0;">
                                @if($company->logo)<img src="{{ asset('storage/'.$company->logo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">@else<i class="fas fa-building"></i>@endif
                            </div>
                            <span style="font-weight:600;">{{ $company->company_name }}</span>
                        </div>
                    </td>
                    <td>{{ $company->manager_name }}</td>
                    <td style="direction:ltr;text-align:right;font-size:0.85rem;">{{ $company->company_email }}</td>
                    <td><span class="badge badge-{{ $company->statusColor() }}">{{ $company->statusLabel() }}</span></td>
                    <td style="font-size:0.8rem;color:var(--text-3);">{{ $company->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fas fa-building"></i><h3>لا توجد نتائج</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
    <div class="pagination-wrap">
        <span>عرض {{ $companies->firstItem() }}–{{ $companies->lastItem() }} من {{ $companies->total() }}</span>
        <div class="pagination">{{ $companies->links() }}</div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('statusPie'), {
    type: 'doughnut',
    data: {
        labels: ['نشطة', 'قيد المراجعة', 'معطلة'],
        datasets: [{
            data: [{{ $statusCounts['active'] ?? 0 }}, {{ $statusCounts['pending'] ?? 0 }}, {{ $statusCounts['inactive'] ?? 0 }}],
            backgroundColor: ['#10B981','#F59E0B','#EF4444'],
            borderWidth: 0, hoverOffset: 6,
        }]
    },
    options: { responsive:true, cutout:'65%', plugins:{ legend:{ display:false } } }
});
</script>
@endpush



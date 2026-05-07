@extends('layouts.admin')

@section('title', 'تقرير المستخدمين | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تقرير المستخدمين</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تقرير المستخدمين</h1>
        <p class="page-subtitle">{{ $users->total() }} نتيجة بناءً على الفلاتر الحالية</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px;">
    <div class="stat-card primary">
        <div class="stat-header"><span class="stat-label">مدراء عامون</span><span class="stat-icon primary"><i class="fas fa-shield-halved"></i></span></div>
        <div class="stat-value">{{ $roleCounts['super_admin'] ?? 0 }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-header"><span class="stat-label">مدراء شركات</span><span class="stat-icon warning"><i class="fas fa-building"></i></span></div>
        <div class="stat-value">{{ $roleCounts['company_admin'] ?? 0 }}</div>
    </div>
    <div class="stat-card info">
        <div class="stat-header"><span class="stat-label">مستخدمون عاديون</span><span class="stat-icon info"><i class="fas fa-user"></i></span></div>
        <div class="stat-value">{{ $roleCounts['user'] ?? 0 }}</div>
    </div>
    <div class="card" style="padding:22px;display:flex;align-items:center;justify-content:center;">
        <canvas id="rolePie" style="max-height:120px;"></canvas>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('admin.reports.users') }}" id="reportForm">
        <div class="filter-bar">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            <input type="date" name="to"   class="form-control" value="{{ request('to') }}">
            <select name="role" class="form-control" onchange="document.getElementById('reportForm').submit()">
                <option value="">جميع الأدوار</option>
                <option value="super_admin"   {{ request('role') === 'super_admin'   ? 'selected' : '' }}>مدير عام</option>
                <option value="company_admin" {{ request('role') === 'company_admin' ? 'selected' : '' }}>مدير شركة</option>
                <option value="user"          {{ request('role') === 'user'          ? 'selected' : '' }}>مستخدم</option>
            </select>
            @if(request()->hasAny(['from','to','role']))
                <a href="{{ route('admin.reports.users') }}" class="btn btn-secondary btn-sm"><i class="fas fa-rotate"></i> إعادة ضبط</a>
            @endif
            <button type="submit" class="btn btn-primary btn-sm" style="margin-right:auto;"><i class="fas fa-filter"></i> تطبيق</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th><th>المستخدم</th><th>البريد</th><th>الدور</th><th>الشركة</th><th>تاريخ التسجيل</th>
                </tr>
            </thead>
            <tbody>
                @php $roleMap = ['super_admin' => ['label'=>'مدير عام','class'=>'primary'], 'company_admin' => ['label'=>'مدير شركة','class'=>'warning'], 'user' => ['label'=>'مستخدم','class'=>'secondary']]; @endphp
                @forelse($users as $i => $user)
                @php $r = $roleMap[$user->role] ?? ['label'=>$user->role,'class'=>'secondary']; @endphp
                <tr>
                    <td style="color:var(--text-3);font-size:0.8rem;">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;gap:10px;align-items:center;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;font-weight:700;flex-shrink:0;">{{ mb_substr($user->name,0,1) }}</div>
                            <span style="font-weight:600;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="direction:ltr;text-align:right;font-size:0.85rem;">{{ $user->email }}</td>
                    <td><span class="badge badge-{{ $r['class'] }}">{{ $r['label'] }}</span></td>
                    <td style="font-size:0.85rem;">{{ $user->company?->company_name ?? '—' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-3);">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fas fa-users"></i><h3>لا توجد نتائج</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrap">
        <span>عرض {{ $users->firstItem() }}–{{ $users->lastItem() }} من {{ $users->total() }}</span>
        <div class="pagination">{{ $users->links() }}</div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('rolePie'), {
    type: 'doughnut',
    data: {
        labels: ['مدير عام', 'مدير شركة', 'مستخدم'],
        datasets: [{
            data: [{{ $roleCounts['super_admin'] ?? 0 }}, {{ $roleCounts['company_admin'] ?? 0 }}, {{ $roleCounts['user'] ?? 0 }}],
            backgroundColor: ['#6C3FC5','#F59E0B','#3B82F6'],
            borderWidth: 0, hoverOffset: 6,
        }]
    },
    options: { responsive:true, cutout:'65%', plugins:{ legend:{ display:false } } }
});
</script>
@endpush



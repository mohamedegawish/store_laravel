@extends('layouts.admin')

@section('title', 'لوحة التحكم | المدير العام')
@section('meta_description', 'إحصائيات ونشاط النظام في الوقت الفعلي')

@section('breadcrumb')
    <span class="current">لوحة التحكم</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">لوحة التحكم 👋</h1>
        <p class="page-subtitle">مرحباً {{ auth()->user()->name }}، إليك ملخص النظام اليوم</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة شركة
        </a>
    </div>
</div>

{{-- ===== STAT CARDS ===== --}}
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-header">
            <span class="stat-label">إجمالي الشركات</span>
            <span class="stat-icon primary"><i class="fas fa-building"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $stats['total_companies'] }}">0</div>
        <div class="stat-trend"><i class="fas fa-arrow-left" style="transform:rotate(-45deg)"></i> جميع الشركات المسجلة</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-header">
            <span class="stat-label">الشركات النشطة</span>
            <span class="stat-icon accent"><i class="fas fa-circle-check"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $stats['active_companies'] }}">0</div>
        <div class="stat-trend"><i class="fas fa-shield-check"></i> مفعّلة ومعتمدة</div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <span class="stat-label">قيد المراجعة</span>
            <span class="stat-icon warning"><i class="fas fa-clock"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $stats['pending_companies'] }}">0</div>
        <div class="stat-trend"><i class="fas fa-hourglass-half"></i> تنتظر الموافقة</div>
    </div>

    <div class="stat-card danger">
        <div class="stat-header">
            <span class="stat-label">الشركات المعطلة</span>
            <span class="stat-icon danger"><i class="fas fa-ban"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $stats['inactive_companies'] }}">0</div>
        <div class="stat-trend"><i class="fas fa-exclamation-circle"></i> معطلة مؤقتاً</div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <span class="stat-label">إجمالي المستخدمين</span>
            <span class="stat-icon info"><i class="fas fa-users"></i></span>
        </div>
        <div class="stat-value" data-count="{{ $stats['total_users'] }}">0</div>
        <div class="stat-trend"><i class="fas fa-user-plus"></i> مسجلون في النظام</div>
    </div>
</div>

{{-- ===== CHARTS ===== --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 28px;">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line"></i> نمو الشركات (12 شهر)</h3>
        </div>
        <div class="card-body">
            <canvas id="companiesChart" height="200"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> نمو المستخدمين (12 شهر)</h3>
        </div>
        <div class="card-body">
            <canvas id="usersChart" height="200"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-pie"></i> توزيع حالات الشركات</h3>
        </div>
        <div class="card-body" style="display:flex; align-items:center; justify-content:center;">
            <canvas id="statusChart" height="200" style="max-width:260px;"></canvas>
        </div>
    </div>
</div>

{{-- ===== RECENT ACTIVITY ===== --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px;">

    {{-- Latest Companies --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building"></i> أحدث الشركات</h3>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-secondary">عرض الكل</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>الشركة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestCompanies as $company)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:0.9rem;flex-shrink:0;overflow:hidden;">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <i class="fas fa-building"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;">{{ $company->company_name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-3);">{{ $company->manager_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $company->statusColor() }}">
                                {{ $company->statusLabel() }}
                            </span>
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-3);">
                            {{ $company->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; color:var(--text-3); padding:30px;">لا توجد شركات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Latest Users --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> أحدث المستخدمين</h3>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">عرض الكل</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>المستخدم</th>
                        <th>الدور</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestUsers as $user)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;">{{ $user->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-3);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleLabels = ['super_admin' => ['label' => 'مدير عام', 'class' => 'primary'], 'company_admin' => ['label' => 'مدير شركة', 'class' => 'warning'], 'user' => ['label' => 'مستخدم', 'class' => 'secondary']];
                                $r = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'secondary'];
                            @endphp
                            <span class="badge badge-{{ $r['class'] }}">{{ $r['label'] }}</span>
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-3);">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; color:var(--text-3); padding:30px;">لا يوجد مستخدمون بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===== ANIMATED COUNTERS =====
document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count);
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current.toLocaleString('ar');
        if (current >= target) clearInterval(timer);
    }, 30);
});

// ===== CHART DATA FROM BLADE =====
const chartLabels    = @json($chartLabels);
const companiesData  = @json($companiesChartData);
const usersData      = @json($usersChartData);

const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#6C3FC5';
const accentColor  = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim()  || '#10B981';

// line chart options
const lineOpts = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0, font: { family: 'Cairo', size: 11 } } }
    }
};

// Companies growth
new Chart(document.getElementById('companiesChart'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            data: companiesData,
            borderColor: '#6C3FC5',
            backgroundColor: 'rgba(108,63,197,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6C3FC5',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: lineOpts
});

// Users growth
new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            data: usersData,
            backgroundColor: 'rgba(16,185,129,0.75)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0, font: { family: 'Cairo', size: 11 } } }
        }
    }
});

// Status doughnut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['نشطة', 'قيد المراجعة', 'معطلة'],
        datasets: [{
            data: [{{ $stats['active_companies'] }}, {{ $stats['pending_companies'] }}, {{ $stats['inactive_companies'] }}],
            backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        cutout: '72%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { family: 'Cairo', size: 12 }, padding: 12, usePointStyle: true }
            }
        }
    }
});
</script>
@endpush



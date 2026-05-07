@extends('layouts.admin')

@section('title', 'سجل النشاط | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">سجل النشاط</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">سجل النشاط</h1>
        <p class="page-subtitle">{{ $logs->total() }} إجراء مسجل</p>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('admin.activity-logs') }}" id="logForm">
        <div class="filter-bar">
            <select name="action" class="form-control" onchange="document.getElementById('logForm').submit()">
                <option value="">جميع الإجراءات</option>
                @foreach(['created','updated','deleted','active','inactive','login'] as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            <input type="date" name="to"   class="form-control" value="{{ request('to') }}">
            @if(request()->hasAny(['action','from','to']))
                <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary btn-sm"><i class="fas fa-rotate"></i> إعادة ضبط</a>
            @endif
            <button type="submit" class="btn btn-primary btn-sm" style="margin-right:auto;"><i class="fas fa-filter"></i> تطبيق</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>الإجراء</th>
                    <th>الوصف</th>
                    <th>المستخدم</th>
                    <th>عنوان IP</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $actionStyles = [
                        'created'  => ['class' => 'accent',  'icon' => 'fa-plus'],
                        'updated'  => ['class' => 'info',    'icon' => 'fa-pen'],
                        'deleted'  => ['class' => 'danger',  'icon' => 'fa-trash-can'],
                        'active'   => ['class' => 'accent',  'icon' => 'fa-circle-check'],
                        'inactive' => ['class' => 'warning', 'icon' => 'fa-ban'],
                        'login'    => ['class' => 'primary', 'icon' => 'fa-right-to-bracket'],
                    ];
                    $style = $actionStyles[$log->action] ?? ['class' => 'secondary', 'icon' => 'fa-circle'];
                @endphp
                <tr>
                    <td>
                        <span class="badge badge-{{ $style['class'] }}">
                            <i class="fas {{ $style['icon'] }}"></i> {{ $log->action }}
                        </span>
                    </td>
                    <td style="font-size:0.875rem; max-width:320px;">{{ $log->description }}</td>
                    <td>
                        @if($log->user)
                            <div style="display:flex;gap:8px;align-items:center;">
                                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:0.72rem;font-weight:700;flex-shrink:0;">
                                    {{ mb_substr($log->user->name, 0, 1) }}
                                </div>
                                <span style="font-size:0.85rem;font-weight:600;">{{ $log->user->name }}</span>
                            </div>
                        @else
                            <span style="color:var(--text-3);font-size:0.85rem;">النظام</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-3);direction:ltr;text-align:right;">{{ $log->ip_address ?? '—' }}</td>
                    <td style="font-size:0.78rem;color:var(--text-3);white-space:nowrap;">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                        <br>
                        <small>{{ $log->created_at->diffForHumans() }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-clock-rotate-left"></i>
                            <h3>لا يوجد نشاط</h3>
                            <p>لم يتم تسجيل أي إجراءات بعد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination-wrap">
        <span>عرض {{ $logs->firstItem() }}–{{ $logs->lastItem() }} من {{ $logs->total() }}</span>
        <div class="pagination">{{ $logs->links() }}</div>
    </div>
    @endif
</div>

@endsection



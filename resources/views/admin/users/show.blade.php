@extends('layouts.admin')

@section('title', $user->name . ' | تفاصيل المستخدم')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.users.index') }}">المستخدمون</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">{{ $user->name }}</span>
@endsection

@section('content')

@php
    $roleMap = ['super_admin' => ['label' => 'مدير عام', 'class' => 'primary', 'icon' => 'fa-shield-halved'], 'company_admin' => ['label' => 'مدير شركة', 'class' => 'warning', 'icon' => 'fa-building'], 'user' => ['label' => 'مستخدم', 'class' => 'secondary', 'icon' => 'fa-user']];
    $r = $roleMap[$user->role] ?? ['label' => $user->role, 'class' => 'secondary', 'icon' => 'fa-user'];
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $user->name }}</h1>
        <p class="page-subtitle">ملف المستخدم التفصيلي</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
            <i class="fas fa-pen"></i> تعديل
        </a>
        @if($user->id !== auth()->id())
        <button class="btn btn-danger" onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ $user->name }}')">
            <i class="fas fa-trash-can"></i> حذف
        </button>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 280px 1fr; gap: 22px; align-items: start;">

    <div class="card">
        <div class="card-body" style="text-align:center; padding:28px;">
            <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:800;margin:0 auto 16px;">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:6px;">{{ $user->name }}</h2>
            <p style="font-size:0.85rem;color:var(--text-3);margin-bottom:12px; direction:ltr;">{{ $user->email }}</p>
            <span class="badge badge-{{ $r['class'] }}" style="font-size:0.8rem;">
                <i class="fas {{ $r['icon'] }}"></i> {{ $r['label'] }}
            </span>
        </div>
        <div class="card-footer">
            <div style="font-size:0.78rem; color:var(--text-3); display:flex; flex-direction:column; gap:6px;">
                <div><i class="fas fa-calendar-plus" style="margin-left:6px;"></i> انضم {{ $user->created_at->format('d/m/Y') }}</div>
                <div><i class="fas fa-clock" style="margin-left:6px;"></i> {{ $user->created_at->diffForHumans() }}</div>
                @if($user->company)
                    <div><i class="fas fa-building" style="margin-left:6px;"></i>
                        <a href="{{ route('admin.companies.show', $user->company) }}" style="color:var(--primary);">{{ $user->company->company_name }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-circle-info"></i> معلومات الحساب</h3></div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                @php
                    $infoItems = [
                        ['label' => 'الاسم الكامل', 'icon' => 'fa-user', 'value' => $user->name],
                        ['label' => 'البريد الإلكتروني', 'icon' => 'fa-envelope', 'value' => $user->email],
                        ['label' => 'الدور الوظيفي', 'icon' => 'fa-id-badge', 'value' => $r['label']],
                        ['label' => 'الشركة', 'icon' => 'fa-building', 'value' => $user->company?->company_name ?? '—'],
                        ['label' => 'تاريخ الانضمام', 'icon' => 'fa-calendar', 'value' => $user->created_at->format('d/m/Y H:i')],
                        ['label' => 'آخر تحديث', 'icon' => 'fa-pen-to-square', 'value' => $user->updated_at->diffForHumans()],
                    ];
                @endphp
                @foreach($infoItems as $item)
                <div style="display:flex;gap:12px;align-items:flex-start;padding:12px;background:var(--surface-2);border-radius:var(--radius-sm);">
                    <div style="width:32px;height:32px;background:var(--primary-soft);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:0.8rem;flex-shrink:0;">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:var(--text-3);margin-bottom:2px;">{{ $item['label'] }}</div>
                        <div style="font-weight:600;font-size:0.875rem;">{{ $item['value'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection



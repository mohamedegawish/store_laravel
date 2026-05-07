@extends('layouts.admin')

@section('title', $company->company_name . ' | تفاصيل الشركة')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.companies.index') }}">الشركات</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">{{ $company->company_name }}</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $company->company_name }}</h1>
        <p class="page-subtitle">تفاصيل ومعلومات الشركة الكاملة</p>
    </div>
    <div class="btn-group">
        <form action="{{ route('admin.companies.toggleStatus', $company) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn {{ $company->isActive() ? 'btn-warning' : 'btn-success' }}">
                <i class="fas {{ $company->isActive() ? 'fa-ban' : 'fa-circle-check' }}"></i>
                {{ $company->isActive() ? 'تعطيل الشركة' : 'تفعيل الشركة' }}
            </button>
        </form>
        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-outline-primary">
            <i class="fas fa-pen"></i> تعديل
        </a>
        <button class="btn btn-danger" onclick="confirmDelete('{{ route('admin.companies.destroy', $company) }}', '{{ $company->company_name }}')">
            <i class="fas fa-trash-can"></i> حذف
        </button>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 300px 1fr; gap: 22px; align-items: start;">

    {{-- LEFT COLUMN: logo + status --}}
    <div style="display:flex; flex-direction:column; gap:18px;">
        <div class="card">
            <div class="card-body" style="text-align:center;">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}"
                         alt="{{ $company->company_name }}"
                         style="width:120px;height:120px;border-radius:16px;object-fit:cover;margin-bottom:16px;box-shadow:var(--shadow);">
                @else
                    <div style="width:120px;height:120px;border-radius:16px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:3rem;color:var(--primary);">
                        <i class="fas fa-building"></i>
                    </div>
                @endif

                <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:6px;">{{ $company->company_name }}</h2>
                <span class="badge badge-{{ $company->statusColor() }}" style="font-size:0.8rem;">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                    {{ $company->statusLabel() }}
                </span>

                <div style="margin-top:18px; text-align:right; display:flex; flex-direction:column; gap:10px;">
                    @if($company->website)
                    <a href="{{ $company->website }}" target="_blank" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        <i class="fas fa-globe"></i> الموقع الإلكتروني
                    </a>
                    @endif
                    @if($company->whatsapp)
                    <a href="https://wa.me/{{ $company->whatsapp }}" target="_blank" class="btn btn-success btn-sm" style="width:100%; justify-content:center; background:#25D366;">
                        <i class="fab fa-whatsapp"></i> واتساب
                    </a>
                    @endif
                    @if($company->location_url)
                    <a href="{{ $company->location_url }}" target="_blank" class="btn btn-info btn-sm" style="width:100%; justify-content:center; background:var(--info); color:white;">
                        <i class="fas fa-location-dot"></i> الموقع على الخريطة
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div style="font-size:0.78rem; color:var(--text-3); display:flex; flex-direction:column; gap:4px;">
                    <span><i class="fas fa-calendar-plus" style="margin-left:5px;"></i> أُضيفت {{ $company->created_at->format('d/m/Y') }}</span>
                    <span><i class="fas fa-pen" style="margin-left:5px;"></i> آخر تحديث {{ $company->updated_at->diffForHumans() }}</span>
                    @if($company->user)
                    <span><i class="fas fa-user" style="margin-left:5px;"></i> مرتبطة بـ {{ $company->user->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Products summary --}}
        <div class="card">
            <div class="card-body" style="text-align:center;">
                <div class="stat-icon primary" style="margin:0 auto 10px;">
                    <i class="fas fa-box-open"></i>
                </div>
                <div style="font-size:1.8rem;font-weight:800;">{{ $company->products->count() }}</div>
                <div style="font-size:0.85rem;color:var(--text-3);">منتج مسجل</div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: details --}}
    <div style="display:flex; flex-direction:column; gap:18px;">

        {{-- Basic info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-circle-info"></i> المعلومات الأساسية</h3>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
                    @php
                        $basicInfo = [
                            ['label' => 'رئيس مجلس الإدارة', 'icon' => 'fa-crown', 'value' => $company->chairman_name],
                            ['label' => 'المدير التنفيذي', 'icon' => 'fa-user-tie', 'value' => $company->manager_name],
                            ['label' => 'هاتف المدير', 'icon' => 'fa-phone', 'value' => $company->manager_phone],
                            ['label' => 'البريد الإلكتروني', 'icon' => 'fa-envelope', 'value' => $company->company_email],
                            ['label' => 'الخط الساخن', 'icon' => 'fa-headset', 'value' => $company->hotline ?? '—'],
                            ['label' => 'واتساب', 'icon' => 'fab fa-whatsapp', 'value' => $company->whatsapp ?? '—'],
                            ['label' => 'عنوان المصنع', 'icon' => 'fa-industry', 'value' => $company->factory_address],
                            ['label' => 'رابط المنتجات', 'icon' => 'fa-link', 'value' => $company->product_link ?? '—'],
                        ];
                    @endphp

                    @foreach($basicInfo as $info)
                    <div style="display:flex; gap:12px; align-items:flex-start; padding:12px; background:var(--surface-2); border-radius:var(--radius-sm);">
                        <div style="width:34px;height:34px;background:var(--primary-soft);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:0.85rem;flex-shrink:0;">
                            <i class="fas {{ $info['icon'] }}"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;color:var(--text-3);margin-bottom:2px;">{{ $info['label'] }}</div>
                            <div style="font-weight:600;font-size:0.875rem;">{{ $info['value'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Branches & hours --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-store"></i> الفروع والمعارض وساعات العمل</h3>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                    <div style="padding:14px; background:var(--surface-2); border-radius:var(--radius-sm);">
                        <div style="font-size:0.75rem;color:var(--text-3);margin-bottom:4px;"><i class="fas fa-code-branch" style="margin-left:4px;"></i> الفروع</div>
                        <div style="font-weight:600;">{{ $company->branches ?: '—' }}</div>
                        @if($company->branches_working_hours)
                        <div style="font-size:0.78rem;color:var(--text-3);margin-top:4px;"><i class="fas fa-clock" style="margin-left:3px;"></i> {{ $company->branches_working_hours }}</div>
                        @endif
                    </div>
                    <div style="padding:14px; background:var(--surface-2); border-radius:var(--radius-sm);">
                        <div style="font-size:0.75rem;color:var(--text-3);margin-bottom:4px;"><i class="fas fa-shop" style="margin-left:4px;"></i> المعارض</div>
                        <div style="font-weight:600;">{{ $company->exhibitions ?: '—' }}</div>
                        @if($company->exhibitions_working_hours)
                        <div style="font-size:0.78rem;color:var(--text-3);margin-top:4px;"><i class="fas fa-clock" style="margin-left:3px;"></i> {{ $company->exhibitions_working_hours }}</div>
                        @endif
                    </div>
                    <div style="padding:14px; background:var(--surface-2); border-radius:var(--radius-sm);">
                        <div style="font-size:0.75rem;color:var(--text-3);margin-bottom:4px;"><i class="fas fa-clock" style="margin-left:4px;"></i> ساعات العمل الرئيسية</div>
                        <div style="font-weight:600;">{{ $company->company_working_hours ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection



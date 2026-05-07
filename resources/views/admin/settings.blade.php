@extends('layouts.admin')

@section('title', 'إعدادات النظام | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">الإعدادات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إعدادات النظام</h1>
        <p class="page-subtitle">تكوين الإعدادات العامة للتطبيق</p>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex; gap:4px; margin-bottom:20px; background:var(--surface); padding:6px; border-radius:var(--radius); border:1px solid var(--border); width:fit-content;">
    <button class="tab-btn active" onclick="switchTab('general', this)"><i class="fas fa-sliders"></i> عام</button>
    <button class="tab-btn" onclick="switchTab('security', this)"><i class="fas fa-shield-halved"></i> الأمان</button>
    <button class="tab-btn" onclick="switchTab('info', this)"><i class="fas fa-info-circle"></i> معلومات الموقع</button>
</div>

<style>
.tab-btn { padding:8px 18px; border:none; border-radius:var(--radius-sm); background:transparent; color:var(--text-2); font-family:var(--font); font-size:0.875rem; font-weight:600; cursor:pointer; transition:var(--transition); display:flex; align-items:center; gap:6px; }
.tab-btn.active { background:var(--primary); color:white; box-shadow:0 4px 12px var(--primary-glow); }
.tab-panel { display:none; }
.tab-panel.active { display:block; }
</style>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    {{-- General tab --}}
    <div class="tab-panel active" id="tab-general">
        <div class="card" style="max-width:700px;">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-sliders"></i> الإعدادات العامة</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">اسم الموقع</label>
                    <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? config('app.name') }}" placeholder="اسم التطبيق">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني للنظام</label>
                    <input type="email" name="site_email" class="form-control" value="{{ $settings['site_email'] ?? '' }}" placeholder="admin@yoursite.com" dir="ltr">
                </div>
                <div class="form-group">
                    <label class="form-label">عدد العناصر في كل صفحة</label>
                    <select name="pagination_limit" class="form-control">
                        @foreach([10, 15, 20, 25, 50, 100] as $limit)
                            <option value="{{ $limit }}" {{ ($settings['pagination_limit'] ?? 15) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                        @endforeach
                    </select>
                    <p class="form-hint">يؤثر على جميع قوائم الجداول في الإدارة</p>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="hidden" name="allow_registration" value="0">
                        <input type="checkbox" name="allow_registration" value="1"
                               {{ ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width:18px;height:18px;accent-color:var(--primary);">
                        <span>السماح بالتسجيل العام</span>
                    </label>
                    <p class="form-hint">إذا عُطِّل، لن يتمكن المستخدمون الجدد من إنشاء حسابات</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Security tab --}}
    <div class="tab-panel" id="tab-security">
        <div class="card" style="max-width:700px;">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-shield-halved"></i> إعدادات الأمان</h3></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-circle-info"></i>
                    <span>إعدادات الأمان المتقدمة (2FA، JWT، Session lifetime) تتطلب تعديل ملفات الإعداد مباشرةً.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف للتواصل</label>
                    <input type="text" name="site_phone" class="form-control" value="{{ $settings['site_phone'] ?? '' }}" placeholder="+20 xx xxxx xxxx" dir="ltr">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">عنوان المقر الرئيسي</label>
                    <textarea name="site_address" class="form-control" rows="3" placeholder="عنوان الشركة الكامل">{{ $settings['site_address'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Info tab --}}
    <div class="tab-panel" id="tab-info">
        <div class="card" style="max-width:700px;">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> معلومات البيئة</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                    @php
                        $sysInfo = [
                            ['label' => 'إصدار Laravel', 'value' => app()->version()],
                            ['label' => 'إصدار PHP', 'value' => PHP_VERSION],
                            ['label' => 'بيئة التشغيل', 'value' => app()->environment()],
                            ['label' => 'المنطقة الزمنية', 'value' => config('app.timezone')],
                            ['label' => 'اللغة الافتراضية', 'value' => config('app.locale')],
                            ['label' => 'محرك قاعدة البيانات', 'value' => config('database.default')],
                        ];
                    @endphp
                    @foreach($sysInfo as $item)
                    <div style="padding:12px;background:var(--surface-2);border-radius:var(--radius-sm);">
                        <div style="font-size:0.72rem;color:var(--text-3);margin-bottom:3px;">{{ $item['label'] }}</div>
                        <div style="font-weight:700;font-size:0.9rem;direction:ltr;text-align:right;">{{ $item['value'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:18px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-floppy-disk"></i> حفظ الإعدادات
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush



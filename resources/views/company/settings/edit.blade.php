@extends('layouts.company')

@section('title', 'تخصيص المتجر | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تخصيص المتجر</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تخصيص المتجر 🎨</h1>
        <p class="page-subtitle">تحكم كامل في هوية ومظهر متجرك العام</p>
    </div>
    @if($company->slug)
    <a href="{{ route('front.home', $company->slug) }}" target="_blank" class="btn btn-outline-primary">
        <i class="fas fa-eye"></i> معاينة المتجر
    </a>
    @endif
</div>

<form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
    @csrf @method('PUT')

    {{-- Live Preview Bar --}}
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div id="previewSwatch" style="width:36px; height:36px; border-radius:8px; transition:.3s; background: {{ $settings->primary_color }};"></div>
            <div>
                <div style="font-weight:700; font-size:0.9rem; color:var(--text);">معاينة لحظية للألوان</div>
                <div style="font-size:0.75rem; color:var(--text-3);">اختر الألوان وشاهد التأثير فوراً</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> حفظ جميع الإعدادات</button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

        {{-- ── BRANDING ── --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-paintbrush"></i> هوية المتجر (الألوان)</h3></div>
                <div class="card-body">

                    <div class="form-group">
                        <label class="form-label">اللون الرئيسي</label>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="color" name="primary_color" id="primaryColorPicker"
                                   value="{{ old('primary_color', $settings->primary_color) }}"
                                   style="width:52px;height:44px;border:none;border-radius:10px;cursor:pointer;padding:2px;background:none;">
                            <input type="text" id="primaryColorText" class="form-control"
                                   value="{{ old('primary_color', $settings->primary_color) }}"
                                   style="font-family:monospace; font-size:1rem; max-width:140px;"
                                   oninput="syncColor(this, 'primaryColorPicker')">
                        </div>
                        <div class="form-hint">يُستخدم في الأزرار والروابط والعناصر الرئيسية.</div>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label class="form-label">اللون الثانوي (خلفيات فاتحة)</label>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="color" name="secondary_color" id="secondaryColorPicker"
                                   value="{{ old('secondary_color', $settings->secondary_color) }}"
                                   style="width:52px;height:44px;border:none;border-radius:10px;cursor:pointer;padding:2px;background:none;">
                            <input type="text" id="secondaryColorText" class="form-control"
                                   value="{{ old('secondary_color', $settings->secondary_color) }}"
                                   style="font-family:monospace; font-size:1rem; max-width:140px;"
                                   oninput="syncColor(this, 'secondaryColorPicker')">
                        </div>
                        <div class="form-hint">يُستخدم في خلفيات البطاقات والأقسام.</div>
                    </div>

                    {{-- Preset Palettes --}}
                    <div style="margin-top:20px;">
                        <div class="form-label">ألوان جاهزة</div>
                        <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;">
                            @foreach([
                                ['#6C3FC5','#EFE9FA'],['#0EA5E9','#E0F2FE'],['#10B981','#D1FAE5'],
                                ['#F59E0B','#FEF3C7'],['#EF4444','#FEE2E2'],['#8B5CF6','#EDE9FE'],
                                ['#EC4899','#FCE7F3'],['#14B8A6','#CCFBF1'],['#1E293B','#F1F5F9'],
                            ] as [$p, $s])
                            <button type="button" onclick="applyPalette('{{ $p }}','{{ $s }}')"
                                    style="width:26px;height:26px;border-radius:50%;background:{{ $p }};border:2px solid var(--border);cursor:pointer;transition:.2s;"
                                    title="{{ $p }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-font"></i> الخط والمظهر</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">نوع الخط</label>
                        <select name="font_family" class="form-control" id="fontSelect">
                            @foreach(['Cairo'=>'Cairo (عربي عصري)','Tajawal'=>'Tajawal (رشيق وحديث)','Almarai'=>'Almarai (أنيق وجميل)','IBM Plex Sans Arabic'=>'IBM Plex (احترافي)','Ping-Medium'=>'Ping Medium (الافتراضي)'] as $val => $label)
                                <option value="{{ $val }}" {{ old('font_family', $settings->font_family) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label">وضع السمة الافتراضي</label>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-top:8px;">
                            @foreach(['light'=>['☀️','فاتح'],'dark'=>['🌙','داكن'],'system'=>['🖥️','ذكي']] as $val => [$emoji, $label])
                            <label style="display:flex; flex-direction:column; align-items:center; gap:6px; padding:12px; border:2px solid {{ old('theme_mode', $settings->theme_mode) == $val ? 'var(--primary)' : 'var(--border)' }}; border-radius:var(--radius); cursor:pointer; transition:.2s;" class="theme-option">
                                <input type="radio" name="theme_mode" value="{{ $val }}" {{ old('theme_mode', $settings->theme_mode) == $val ? 'checked' : '' }} style="display:none;" onchange="highlightSelected(this)">
                                <span style="font-size:1.5rem;">{{ $emoji }}</span>
                                <span style="font-size:0.8rem; font-weight:600;">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SMART FEATURES & BANNERS ── --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-wand-magic-sparkles"></i> مزايا ذكية</h3></div>
                <div class="card-body">
                    <label style="display:flex; align-items:flex-start; gap:14px; cursor:pointer; padding:14px; border:1px solid var(--border); border-radius:var(--radius); margin-bottom:12px; transition:.2s;" onmouseenter="this.style.borderColor='var(--primary)'" onmouseleave="this.style.borderColor='var(--border)'">
                        <input type="checkbox" name="show_trending_badges" value="1"
                               {{ old('show_trending_badges', $settings->show_trending_badges) ? 'checked' : '' }}
                               style="width:18px;height:18px;accent-color:var(--primary);margin-top:2px;flex-shrink:0;">
                        <div>
                            <div style="font-weight:700;">🔥 شارة "الأكثر مبيعاً"</div>
                            <div style="font-size:0.8rem; color:var(--text-3); margin-top:3px;">تُعرض تلقائياً على المنتجات الأكثر مبيعاً لزيادة الإقبال.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:14px; cursor:pointer; padding:14px; border:1px solid var(--border); border-radius:var(--radius); transition:.2s;" onmouseenter="this.style.borderColor='var(--primary)'" onmouseleave="this.style.borderColor='var(--border)'">
                        <input type="checkbox" name="show_low_stock_badges" value="1"
                               {{ old('show_low_stock_badges', $settings->show_low_stock_badges) ? 'checked' : '' }}
                               style="width:18px;height:18px;accent-color:var(--primary);margin-top:2px;flex-shrink:0;">
                        <div>
                            <div style="font-weight:700;">⏳ تحذير المخزون المنخفض</div>
                            <div style="font-size:0.8rem; color:var(--text-3); margin-top:3px;">عرض "تبقى X قطع فقط!" لخلق إحساس بالإلحاح وتحفيز الشراء.</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Banners Management --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-images"></i> البانرات والسلايدر</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addBannerRow()">
                        <i class="fas fa-plus"></i> إضافة بانر
                    </button>
                </div>
                <div class="card-body">

                    {{-- Existing Banners --}}
                    @if($settings->banners && count($settings->banners))
                        <div style="margin-bottom:20px;">
                            <div class="form-label" style="margin-bottom:12px;">البانرات الحالية</div>
                            @foreach($settings->banners as $i => $banner)
                            <div style="display:flex; align-items:center; gap:12px; padding:10px; border:1px solid var(--border); border-radius:var(--radius); margin-bottom:8px; background:var(--surface);">
                                <input type="checkbox" name="keep_banners[]" value="{{ $i }}" checked
                                       style="width:16px;height:16px;accent-color:var(--primary); flex-shrink:0;">
                                <img src="{{ asset('storage/'.$banner['image']) }}" style="width:60px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:0.875rem; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $banner['title'] ?: 'بدون عنوان' }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-3);">{{ $banner['url'] ?? '#' }}</div>
                                </div>
                                <label style="font-size:0.75rem; color:var(--danger); cursor:pointer;" title="رفع الصح للحذف">
                                    <i class="fas fa-trash-alt"></i>
                                </label>
                            </div>
                            @endforeach
                            <p style="font-size:0.8rem; color:var(--text-3); margin-top:8px;"><i class="fas fa-info-circle"></i> أزل الصح من البانر لحذفه عند الحفظ.</p>
                        </div>
                    @endif

                    {{-- New Banners Container --}}
                    <div id="newBannersContainer"></div>
                    <p id="bannerHint" style="font-size:0.82rem; color:var(--text-3); margin-top:10px;">
                        <i class="fas fa-lightbulb"></i> البانر الأول سيكون الرئيسي في الصفحة الرئيسية. الحجم المثالي 1400×500px.
                    </p>

                </div>
            </div>
        </div>

    </div>

    {{-- Bottom Save Button --}}
    <div style="display:flex; justify-content:flex-end; margin-top:24px;">
        <button type="submit" class="btn btn-primary" style="padding:12px 40px; font-size:1rem;">
            <i class="fas fa-check"></i> حفظ وتطبيق جميع الإعدادات
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
// ── Color Sync ──────────────────────────────────────────
const primaryPicker   = document.getElementById('primaryColorPicker');
const primaryText     = document.getElementById('primaryColorText');
const secondaryPicker = document.getElementById('secondaryColorPicker');
const secondaryText   = document.getElementById('secondaryColorText');
const previewSwatch   = document.getElementById('previewSwatch');

primaryPicker.addEventListener('input', e => {
    primaryText.value = e.target.value.toUpperCase();
    previewSwatch.style.background = e.target.value;
});
secondaryPicker.addEventListener('input', e => {
    secondaryText.value = e.target.value.toUpperCase();
});

function syncColor(textInput, pickerId) {
    const val = textInput.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.getElementById(pickerId).value = val;
        if (pickerId === 'primaryColorPicker') previewSwatch.style.background = val;
    }
}

function applyPalette(primary, secondary) {
    primaryPicker.value   = primary;
    primaryText.value     = primary.toUpperCase();
    secondaryPicker.value = secondary;
    secondaryText.value   = secondary.toUpperCase();
    previewSwatch.style.background = primary;
}

// ── Theme Option Highlight ──────────────────────────────
function highlightSelected(radio) {
    document.querySelectorAll('.theme-option').forEach(el => {
        el.style.borderColor = 'var(--border)';
    });
    radio.closest('.theme-option').style.borderColor = 'var(--primary)';
}
document.querySelectorAll('input[name="theme_mode"]').forEach(r => {
    r.addEventListener('change', function() { highlightSelected(this); });
});

// ── Add New Banner Row ──────────────────────────────────
let bannerCount = 0;
function addBannerRow() {
    bannerCount++;
    const container = document.getElementById('newBannersContainer');
    const div = document.createElement('div');
    div.style.cssText = 'border:1px dashed var(--border); border-radius:var(--radius); padding:16px; margin-bottom:12px; background:var(--surface)';
    div.innerHTML = `
        <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
            <span style="font-weight:600; font-size:0.875rem;">بانر جديد #${bannerCount}</span>
            <button type="button" onclick="this.closest('div').parentElement.remove()" style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:0.875rem;">
                <i class="fas fa-times"></i> إزالة
            </button>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">صورة البانر <span class="required">*</span></label>
                <input type="file" name="banner_images[]" accept="image/*" class="form-control" required onchange="previewBanner(this)">
                <img class="banner-preview" style="margin-top:8px;width:100%;height:80px;object-fit:cover;border-radius:8px;display:none;border:1px solid var(--border);">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان البانر</label>
                <input type="text" name="banner_titles[]" class="form-control" placeholder="مثال: عروض الصيف الكبرى">
            </div>
            <div class="form-group">
                <label class="form-label">نص الزر</label>
                <input type="text" name="banner_buttons[]" class="form-control" placeholder="مثال: تسوق الآن">
            </div>
            <div class="form-group">
                <label class="form-label">رابط الزر</label>
                <input type="text" name="banner_urls[]" class="form-control" placeholder="/store/${encodeURIComponent('{{ $company->slug ?? "" }}')}/products" dir="ltr">
            </div>
        </div>
    `;
    container.appendChild(div);
}

function previewBanner(input) {
    if (input.files && input.files[0]) {
        const img = input.nextElementSibling;
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

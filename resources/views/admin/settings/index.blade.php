@extends('layouts.admin')
@section('title', 'إعدادات المتجر')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الإعدادات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">إعدادات المتجر</h1><p class="page-subtitle">تخصيص مظهر وإعدادات المتجر</p></div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf

{{-- Tabs --}}
<div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:24px;overflow-x:auto" id="settingsTabs">
    @php $tabs = [
        ['id'=>'branding','icon'=>'fas fa-palette','label'=>'الهوية والألوان'],
        ['id'=>'contact','icon'=>'fas fa-phone','label'=>'التواصل'],
        ['id'=>'commerce','icon'=>'fas fa-store','label'=>'إعدادات المتجر'],
        ['id'=>'homepage','icon'=>'fas fa-home','label'=>'الصفحة الرئيسية'],
        ['id'=>'seo','icon'=>'fas fa-search','label'=>'SEO'],
    ] @endphp
    @foreach($tabs as $tab)
    <button type="button" class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab['id'] }}"
        style="padding:12px 20px;border:none;background:none;font-family:var(--font);font-size:.88rem;font-weight:600;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;transition:var(--tr);white-space:nowrap">
        <i class="{{ $tab['icon'] }}"></i> {{ $tab['label'] }}
    </button>
    @endforeach
</div>

{{-- Tab: Branding --}}
<div class="tab-content" id="tab-branding">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="card">
        <div class="card-header"><span class="card-title">هوية المتجر</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">اسم المتجر (عربي)</label>
                <input type="text" name="store_name_ar" class="form-control" value="{{ old('store_name_ar', $settings->store_name_ar) }}">
            </div>
            <div class="form-group">
                <label class="form-label">اسم المتجر (إنجليزي)</label>
                <input type="text" name="store_name_en" class="form-control" value="{{ old('store_name_en', $settings->store_name_en) }}">
            </div>
            <div class="form-group">
                <label class="form-label">الشعار (Logo)</label>
                @if($settings->logo)
                <img src="{{ asset('storage/'.$settings->logo) }}" alt="logo" style="height:60px;border-radius:8px;margin-bottom:8px;display:block">
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">الأيقونة (Favicon)</label>
                @if($settings->favicon)
                <img src="{{ asset('storage/'.$settings->favicon) }}" alt="favicon" style="height:32px;margin-bottom:8px;display:block">
                @endif
                <input type="file" name="favicon" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">صورة الغلاف</label>
                @if($settings->cover_image)
                <img src="{{ asset('storage/'.$settings->cover_image) }}" alt="cover" style="width:100%;height:80px;object-fit:cover;border-radius:8px;margin-bottom:8px">
                @endif
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">الألوان والخطوط</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">اللون الأساسي</label>
                <div style="display:flex;gap:10px;align-items:center">
                    <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color ?? '#6C3FC5') }}" style="width:50px">
                    <input type="text" id="primaryHex" class="form-control" value="{{ $settings->primary_color ?? '#6C3FC5' }}" style="flex:1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">اللون الثانوي</label>
                <div style="display:flex;gap:10px;align-items:center">
                    <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?? '#EFE9FA') }}" style="width:50px">
                    <input type="text" class="form-control" value="{{ $settings->secondary_color ?? '#EFE9FA' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">لون التأكيد (Accent)</label>
                <div style="display:flex;gap:10px;align-items:center">
                    <input type="color" name="accent_color" value="{{ old('accent_color', $settings->accent_color ?? '#10B981') }}" style="width:50px">
                    <input type="text" class="form-control" value="{{ $settings->accent_color ?? '#10B981' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">الخط</label>
                <select name="font_family" class="form-control form-select">
                    @foreach(['Cairo','Tajawal','Almarai','IBM Plex Sans Arabic'] as $font)
                    <option value="{{ $font }}" {{ ($settings->font_family ?? 'Cairo') === $font ? 'selected' : '' }}>{{ $font }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">المظهر الافتراضي</label>
                <select name="theme_mode" class="form-control form-select">
                    <option value="light" {{ ($settings->theme_mode ?? 'light') === 'light' ? 'selected' : '' }}>فاتح</option>
                    <option value="dark" {{ ($settings->theme_mode ?? 'light') === 'dark' ? 'selected' : '' }}>داكن</option>
                    <option value="system" {{ ($settings->theme_mode ?? 'light') === 'system' ? 'selected' : '' }}>تلقائي</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">اللغة الافتراضية</label>
                <select name="default_locale" class="form-control form-select">
                    <option value="ar" {{ ($settings->default_locale ?? 'ar') === 'ar' ? 'selected' : '' }}>العربية</option>
                    <option value="en" {{ ($settings->default_locale ?? 'ar') === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Tab: Contact --}}
<div class="tab-content" id="tab-contact" style="display:none">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="card">
        <div class="card-header"><span class="card-title">معلومات التواصل</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone) }}">
            </div>
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">العنوان (عربي)</label>
                <textarea name="address_ar" class="form-control" rows="2">{{ old('address_ar', $settings->address_ar) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">العنوان (إنجليزي)</label>
                <textarea name="address_en" class="form-control" rows="2">{{ old('address_en', $settings->address_en) }}</textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">روابط التواصل الاجتماعي</span></div>
        <div class="card-body">
            @foreach([
                'whatsapp'=>['icon'=>'fab fa-whatsapp','label'=>'واتساب'],
                'instagram'=>['icon'=>'fab fa-instagram','label'=>'إنستقرام'],
                'twitter'=>['icon'=>'fab fa-x-twitter','label'=>'تويتر X'],
                'facebook'=>['icon'=>'fab fa-facebook','label'=>'فيسبوك'],
                'tiktok'=>['icon'=>'fab fa-tiktok','label'=>'تيك توك'],
                'snapchat'=>['icon'=>'fab fa-snapchat','label'=>'سناب شات'],
                'youtube'=>['icon'=>'fab fa-youtube','label'=>'يوتيوب'],
            ] as $platform => $info)
            <div class="form-group" style="margin-bottom:12px">
                <label class="form-label" style="display:flex;align-items:center;gap:8px"><i class="{{ $info['icon'] }}"></i> {{ $info['label'] }}</label>
                <input type="url" name="social_links[{{ $platform }}]" class="form-control" value="{{ old('social_links.'.$platform, $settings->social_links[$platform] ?? '') }}" placeholder="https://...">
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>

{{-- Tab: Commerce --}}
<div class="tab-content" id="tab-commerce" style="display:none">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="card">
        <div class="card-header"><span class="card-title">العملة والشحن</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">العملة (كود)</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings->currency ?? 'SAR') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">رمز العملة</label>
                    <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings->currency_symbol ?? 'ر.س') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">تكلفة الشحن</label>
                <input type="number" name="shipping_cost" class="form-control" value="{{ old('shipping_cost', $settings->shipping_cost ?? 0) }}" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">الحد الأدنى للشحن المجاني</label>
                <input type="number" name="free_shipping_threshold" class="form-control" value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold) }}" step="0.01" min="0" placeholder="اتركه فارغاً لإلغاء الشحن المجاني">
            </div>
            <div class="form-group">
                <label class="form-label">نسبة الضريبة %</label>
                <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings->tax_rate ?? 0) }}" step="0.01" min="0" max="100">
            </div>
            <div class="form-group">
                <label class="form-label">الحد الأدنى للطلب</label>
                <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $settings->min_order_amount ?? 0) }}" step="0.01" min="0">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">الميزات والخيارات</span></div>
        <div class="card-body">
            @foreach([
                'enable_reviews'   => 'تفعيل التقييمات',
                'enable_wishlist'  => 'تفعيل المفضلة',
                'enable_newsletter'=> 'تفعيل النشرة البريدية',
                'show_low_stock_badges' => 'إظهار شارة المخزون المنخفض',
                'show_trending_badges'  => 'إظهار شارة الرائج',
                'maintenance_mode' => 'وضع الصيانة',
            ] as $field => $label)
            <label style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;cursor:pointer">
                <span style="font-size:.9rem;font-weight:500">{{ $label }}</span>
                <label class="switch">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $settings->$field ?? false) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </label>
            @endforeach
        </div>
    </div>
</div>
</div>

{{-- Tab: Homepage --}}
<div class="tab-content" id="tab-homepage" style="display:none">
<div class="card">
    <div class="card-header"><span class="card-title">محتوى الصفحة الرئيسية (Hero)</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label">العنوان الرئيسي (عربي)</label>
                <input type="text" name="hero_title_ar" class="form-control" value="{{ old('hero_title_ar', $settings->hero_title_ar) }}" placeholder="مثال: تسوق بذوق رفيع">
            </div>
            <div class="form-group">
                <label class="form-label">العنوان الرئيسي (إنجليزي)</label>
                <input type="text" name="hero_title_en" class="form-control" value="{{ old('hero_title_en', $settings->hero_title_en) }}" placeholder="e.g. Shop with Style">
            </div>
            <div class="form-group">
                <label class="form-label">العنوان الفرعي (عربي)</label>
                <textarea name="hero_subtitle_ar" class="form-control" rows="2" placeholder="وصف مختصر جذاب...">{{ old('hero_subtitle_ar', $settings->hero_subtitle_ar) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">العنوان الفرعي (إنجليزي)</label>
                <textarea name="hero_subtitle_en" class="form-control" rows="2" placeholder="Short catchy description...">{{ old('hero_subtitle_en', $settings->hero_subtitle_en) }}</textarea>
            </div>
        </div>
    </div>
</div>
<div class="card" style="margin-top:20px">
    <div class="card-header">
        <span class="card-title">ترتيب أقسام الصفحة الرئيسية</span>
        <span style="font-size:.82rem;color:var(--text-muted)">اسحب لإعادة الترتيب</span>
    </div>
    <div class="card-body">
        @php $sections = $settings->getSectionsConfig() @endphp
        @php usort($sections, fn($a,$b) => $a['order'] <=> $b['order']) @endphp
        <div id="sectionsList" style="display:flex;flex-direction:column;gap:10px">
            @foreach($sections as $i => $section)
            @php $labels = ['hero'=>'الهيدر والسلايدر','featured'=>'المنتجات المميزة','categories'=>'التصنيفات','trending'=>'المنتجات الرائجة','banners'=>'البانرات','bestseller'=>'الأكثر مبيعاً'] @endphp
            <div class="section-item" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border:1px solid var(--border);border-radius:10px;cursor:move;background:var(--surface)" data-key="{{ $section['key'] }}">
                <div style="display:flex;align-items:center;gap:12px">
                    <i class="fas fa-grip-vertical" style="color:var(--text-muted)"></i>
                    <span style="font-weight:600;font-size:.9rem">{{ $labels[$section['key']] ?? $section['key'] }}</span>
                </div>
                <label class="switch">
                    <input type="checkbox" class="section-toggle" data-key="{{ $section['key'] }}" {{ ($section['enabled'] ?? true) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
                <input type="hidden" name="sections_config[{{ $i }}][key]" value="{{ $section['key'] }}">
                <input type="hidden" name="sections_config[{{ $i }}][enabled]" class="section-enabled-input" value="{{ ($section['enabled'] ?? true) ? '1' : '0' }}">
                <input type="hidden" name="sections_config[{{ $i }}][order]" class="section-order-input" value="{{ $section['order'] }}">
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>

{{-- Tab: SEO --}}
<div class="tab-content" id="tab-seo" style="display:none">
<div class="card">
    <div class="card-header"><span class="card-title">تحسين محركات البحث</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">عنوان الصفحة الرئيسية (Meta Title)</label>
            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $settings->meta_title) }}" maxlength="70">
        </div>
        <div class="form-group">
            <label class="form-label">وصف الصفحة الرئيسية (Meta Description)</label>
            <textarea name="meta_description" class="form-control" rows="3" maxlength="160">{{ old('meta_description', $settings->meta_description) }}</textarea>
        </div>
        <hr style="border-color:var(--border);margin:20px 0">
        <div class="form-group">
            <label class="form-label">نص تذييل الصفحة (عربي)</label>
            <textarea name="footer_text_ar" class="form-control" rows="3">{{ old('footer_text_ar', $settings->footer_text_ar) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">نص تذييل الصفحة (إنجليزي)</label>
            <textarea name="footer_text_en" class="form-control" rows="3">{{ old('footer_text_en', $settings->footer_text_en) }}</textarea>
        </div>
    </div>
</div>
</div>

{{-- Save button --}}
<div style="display:flex;justify-content:flex-end;margin-top:24px;padding-top:20px;border-top:1px solid var(--border)">
    <button type="submit" class="btn btn-primary" style="padding:12px 36px;font-size:1rem"><i class="fas fa-save"></i> حفظ الإعدادات</button>
</div>

</form>

@push('scripts')
<script>
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.color = 'var(--text-muted)';
            b.style.borderBottomColor = 'transparent';
        });
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        this.style.color = 'var(--primary)';
        this.style.borderBottomColor = 'var(--primary)';
        document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
    });
});

// Sync color picker with hex input
document.querySelectorAll('input[type=color]').forEach(picker => {
    const text = picker.nextElementSibling;
    if (!text) return;
    picker.addEventListener('input', () => { if(text) text.value = picker.value; });
    text.addEventListener('input', () => { if(text.value.match(/^#[0-9a-fA-F]{6}$/)) picker.value = text.value; });
});

// Section toggles
document.querySelectorAll('.section-toggle').forEach(cb => {
    cb.addEventListener('change', function() {
        const key = this.dataset.key;
        const input = this.closest('.section-item').querySelector('.section-enabled-input');
        if (input) input.value = this.checked ? '1' : '0';
    });
});
</script>
@endpush
@endsection

@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.products.index') }}">المنتجات</a><span class="sep">/</span>
    <span class="current">إضافة منتج</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="productForm">
@csrf

<div class="page-header">
    <div><h1 class="page-title">إضافة منتج جديد</h1></div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">إلغاء</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المنتج</button>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">

{{-- ── Left Column ── --}}
<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Basic Info --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-info-circle" style="color:var(--primary)"></i> المعلومات الأساسية</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">الاسم بالعربي <span>*</span></label>
                    <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" placeholder="مثال: كرسي مكتبي مريح" required>
                    @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزي <span>*</span></label>
                    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" placeholder="e.g. Ergonomic Office Chair" required>
                    @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">الوصف المختصر (عربي)</label>
                <textarea name="short_description_ar" class="form-control" rows="2" placeholder="وصف مختصر للمنتج...">{{ old('short_description_ar') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف المختصر (إنجليزي)</label>
                <textarea name="short_description_en" class="form-control" rows="2" placeholder="Short product description...">{{ old('short_description_en') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الكامل (عربي)</label>
                <textarea name="description_ar" class="form-control" rows="5" placeholder="الوصف التفصيلي بالعربية...">{{ old('description_ar') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الكامل (إنجليزي)</label>
                <textarea name="description_en" class="form-control" rows="5" placeholder="Full description in English...">{{ old('description_en') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Media --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-images" style="color:var(--primary)"></i> الصور</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">صور المنتج (يمكن رفع أكثر من صورة)</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput">
                <div class="form-hint">JPG, PNG, WebP — الحد الأقصى 4 ميجا لكل صورة</div>
                <div id="imagePreview" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px"></div>
            </div>
        </div>
    </div>

    {{-- Pricing & Inventory --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-coins" style="color:var(--accent)"></i> التسعير والمخزون</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">نموذج التسعير</label>
                <select name="pricing_type" class="form-control form-select">
                    <option value="per_piece">بالقطعة</option>
                    <option value="per_kg">بالكيلو</option>
                    <option value="per_gram">بالجرام</option>
                    <option value="per_liter">باللتر</option>
                    <option value="per_package">بالعبوة</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
                <div class="form-group">
                    <label class="form-label">السعر <span>*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">سعر التكلفة</label>
                    <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">سعر العرض</label>
                    <input type="number" name="offer_price" class="form-control" value="{{ old('offer_price') }}" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">بداية العرض</label>
                    <input type="datetime-local" name="offer_starts_at" class="form-control" value="{{ old('offer_starts_at') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">نهاية العرض</label>
                    <input type="datetime-local" name="offer_ends_at" class="form-control" value="{{ old('offer_ends_at') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">الكمية <span>*</span></label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}" min="0" required>
                    @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">تنبيه مخزون منخفض</label>
                    <input type="number" name="min_stock_alert" class="form-control" value="{{ old('min_stock_alert', 5) }}" min="0">
                </div>
            </div>
        </div>
    </div>

    {{-- SEO --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-search" style="color:var(--info)"></i> تحسين محركات البحث (SEO)</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">عنوان SEO</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="عنوان الصفحة في محركات البحث">
            </div>
            <div class="form-group">
                <label class="form-label">وصف SEO</label>
                <textarea name="meta_description" class="form-control" rows="2" placeholder="وصف قصير لمحركات البحث...">{{ old('meta_description') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ── Right Column ── --}}
<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Organization --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-folder" style="color:var(--warning)"></i> التصنيف والبراند</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">التصنيف <span>*</span></label>
                <select name="category_id" class="form-control form-select" required>
                    <option value="">اختر التصنيف</option>
                    @foreach($categories as $cat)
                        @if(!$cat->parent_id)
                        <optgroup label="{{ $cat->name_ar }}">
                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                            @foreach($cat->children as $sub)
                            <option value="{{ $sub->id }}" {{ old('category_id') == $sub->id ? 'selected' : '' }}>— {{ $sub->name_ar }}</option>
                            @endforeach
                        </optgroup>
                        @endif
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">البراند</label>
                <select name="brand_id" class="form-control form-select">
                    <option value="">بدون براند</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="اتركه فارغاً للتوليد التلقائي">
                </div>
                <div class="form-group">
                    <label class="form-label">الباركود</label>
                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-toggle-on" style="color:var(--accent)"></i> الحالة والتمييز</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">حالة المنتج</label>
                <select name="status" class="form-control form-select">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px;margin-top:12px">
                @foreach(['is_featured'=>'مميز (Featured)','is_trending'=>'رائج (Trending)','is_best_seller'=>'الأكثر مبيعاً'] as $field => $label)
                <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer">
                    <span style="font-size:.88rem;font-weight:500">{{ $label }}</span>
                    <label class="switch">
                        <input type="checkbox" name="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Additional --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-weight-hanging" style="color:var(--text-muted)"></i> خصائص إضافية</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">الوزن (كجم)</label>
                    <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" step="0.001" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">نسبة الضريبة %</label>
                    <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', 0) }}" step="0.01" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label">تسمية الوحدة</label>
                    <input type="text" name="unit_label" class="form-control" value="{{ old('unit_label') }}" placeholder="مثال: كجم، قطعة">
                </div>
                <div class="form-group">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- end grid --}}

<div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline">إلغاء</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المنتج</button>
</div>

</form>

@push('scripts')
<script>
document.getElementById('imageInput')?.addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-preview';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
@endsection

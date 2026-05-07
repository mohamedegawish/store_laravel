@extends('layouts.admin')
@section('title', 'تعديل منتج')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.products.index') }}">المنتجات</a><span class="sep">/</span>
    <span class="current">تعديل</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" id="productForm">
@csrf @method('PUT')

<div class="page-header">
    <div><h1 class="page-title">تعديل: {{ $product->name_ar }}</h1></div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">إلغاء</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">

{{-- Left --}}
<div style="display:flex;flex-direction:column;gap:20px">
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-info-circle" style="color:var(--primary)"></i> المعلومات الأساسية</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">الاسم بالعربي <span>*</span></label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $product->name_ar) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزي <span>*</span></label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $product->name_en) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف المختصر (عربي)</label>
                <textarea name="short_description_ar" class="form-control" rows="2">{{ old('short_description_ar', $product->short_description_ar) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الكامل (عربي)</label>
                <textarea name="description_ar" class="form-control" rows="5">{{ old('description_ar', $product->description_ar) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Current Images --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-images" style="color:var(--primary)"></i> الصور</span></div>
        <div class="card-body">
            @if($product->images && count($product->images))
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px">
                @foreach($product->images as $img)
                <div style="position:relative">
                    <img src="{{ asset('storage/'.$img) }}" class="img-preview" alt="">
                    <label style="position:absolute;top:3px;inset-inline-end:3px;background:rgba(239,68,68,.9);color:#fff;width:18px;height:18px;border-radius:99px;display:flex;align-items:center;justify-content:center;font-size:.7rem;cursor:pointer">
                        <input type="checkbox" name="remove_images[]" value="{{ $img }}" style="display:none">
                        <i class="fas fa-times"></i>
                    </label>
                </div>
                @endforeach
            </div>
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:10px">✓ ضع علامة على الصور لحذفها</div>
            @endif
            <div class="form-group">
                <label class="form-label">إضافة صور جديدة</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput">
                <div id="imagePreview" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px"></div>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-coins" style="color:var(--accent)"></i> التسعير والمخزون</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
                <div class="form-group">
                    <label class="form-label">السعر <span>*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->defaultVariant?->price ?? $product->price) }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">سعر التكلفة</label>
                    <input type="number" name="cost" class="form-control" value="{{ old('cost', $product->defaultVariant?->cost) }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">سعر العرض</label>
                    <input type="number" name="offer_price" class="form-control" value="{{ old('offer_price', $product->defaultVariant?->offer_price) }}" step="0.01" min="0">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">الكمية</label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->defaultVariant?->stock_quantity ?? 0) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">تنبيه مخزون منخفض</label>
                    <input type="number" name="min_stock_alert" class="form-control" value="{{ old('min_stock_alert', $product->defaultVariant?->min_stock_alert ?? 5) }}" min="0">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Right --}}
<div style="display:flex;flex-direction:column;gap:20px">
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
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
                        @foreach($cat->children as $sub)
                        <option value="{{ $sub->id }}" {{ old('category_id', $product->category_id) == $sub->id ? 'selected' : '' }}>— {{ $sub->name_ar }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">البراند</label>
                <select name="brand_id" class="form-control form-select">
                    <option value="">بدون براند</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name_ar }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-toggle-on" style="color:var(--accent)"></i> الحالة والتمييز</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">حالة المنتج</label>
                <select name="status" class="form-control form-select">
                    <option value="active"   {{ old('status', $product->status) === 'active'   ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="draft"    {{ old('status', $product->status) === 'draft'    ? 'selected' : '' }}>مسودة</option>
                </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px;margin-top:12px">
                @foreach(['is_featured'=>'مميز','is_trending'=>'رائج','is_best_seller'=>'الأكثر مبيعاً'] as $field => $label)
                <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer">
                    <span style="font-size:.88rem;font-weight:500">{{ $label }}</span>
                    <label class="switch">
                        <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $product->$field) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

</div>

<div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline">إلغاء</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
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
document.querySelectorAll('input[name="remove_images[]"]').forEach(cb => {
    cb.closest('div').addEventListener('click', function() {
        cb.checked = !cb.checked;
        this.style.opacity = cb.checked ? '0.4' : '1';
    });
});
</script>
@endpush
@endsection

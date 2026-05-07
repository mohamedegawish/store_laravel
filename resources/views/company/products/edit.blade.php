@extends('layouts.company')

@section('title', "تعديل: {$product->name} | إدارة المتجر")

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('company.products.index') }}">المنتجات</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تعديل المنتج</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تعديل المنتج ✏️</h1>
        <p class="page-subtitle">{{ $product->name }}</p>
    </div>
    <a href="{{ route('front.product', ['slug' => auth()->user()->company->slug ?? '', 'product'=>$product->id]) }}" target="_blank" class="btn btn-outline-primary">
        <i class="fas fa-eye"></i> معاينة في المتجر
    </a>
</div>

<form action="{{ route('company.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        {{-- ── MAIN COL ── --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            {{-- Basic Info --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">المعلومات الأساسية</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">اسم المنتج <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label">الوصف الكامل <span class="required">*</span></label>
                        <textarea name="description" class="form-control" rows="8" required>{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">صور المنتج</h3></div>
                <div class="card-body">
                    @if($product->images && count($product->images))
                        <div class="form-label" style="margin-bottom:10px;">الصور الحالية (عند رفع صور جديدة سيتم الإضافة عليها):</div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
                            @foreach($product->images as $img)
                                <div style="position:relative; width:80px; height:80px; border-radius:8px; overflow:hidden; border:1px solid var(--border);">
                                    <img src="{{ asset('storage/'.$img) }}" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">إضافة صور جديدة</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imagesInput">
                        @error('images.*') <div class="form-error">{{ $message }}</div> @enderror
                        
                        <div id="imagePreviewContainer" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:15px;"></div>
                    </div>
                </div>
            </div>

            {{-- Pricing & Inventory --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">التسعير والمخزون</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">سعر البيع الأساسي (ج.م) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $variant?->price) }}" required>
                            @error('price') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">مبلغ الخصم (اختياري)</label>
                            <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', $variant?->discount) }}">
                            @error('discount') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">التكلفة الفعلية (لحساب الأرباح)</label>
                            <input type="number" step="0.01" name="cost" class="form-control" value="{{ old('cost', $variant?->cost) }}">
                            @error('cost') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">كمية المخزون <span class="required">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $variant?->stock_quantity ?? 0) }}" required>
                            @error('stock_quantity') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── SIDE COL ── --}}
        <div style="display: flex; flex-direction: column; gap: 24px; position: sticky; top: 90px;">
            
            <div class="card">
                <div class="card-header"><h3 class="card-title">النشر والتصنيف</h3></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:20px;">
                    
                    <div class="form-group">
                        <label class="form-label">الحالة</label>
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                            <span style="font-weight:600;">منتج نشط (مرئي للعملاء)</span>
                        </label>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--border); margin:0;">

                    <div class="form-group">
                        <label class="form-label">القسم الرئيسي <span class="required">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- اختر القسم --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                </div>
                <div class="card-footer" style="padding:20px; background:var(--surface-2);">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px; font-size:1.05rem;">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('company.products.index') }}" class="btn btn-secondary" style="width:100%; justify-content:center; margin-top:10px;">إلغاء الرجوع</a>
                </div>
            </div>
            
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
document.getElementById('imagesInput').addEventListener('change', function(e) {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    
    if (this.files) {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid var(--border);';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush
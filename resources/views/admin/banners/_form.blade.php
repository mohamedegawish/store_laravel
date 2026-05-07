@php $isEdit = isset($banner); @endphp

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">العنوان بالعربي</label>
        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
               value="{{ old('title_ar', $banner->title_ar ?? '') }}">
        @error('title_ar')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">العنوان بالإنجليزي</label>
        <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
               value="{{ old('title_en', $banner->title_en ?? '') }}">
        @error('title_en')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">الوصف بالعربي</label>
        <textarea name="subtitle_ar" class="form-control" rows="2">{{ old('subtitle_ar', $banner->subtitle_ar ?? '') }}</textarea>
    </div>
    <div class="form-group">
        <label class="form-label">الوصف بالإنجليزي</label>
        <textarea name="subtitle_en" class="form-control" rows="2">{{ old('subtitle_en', $banner->subtitle_en ?? '') }}</textarea>
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">الموضع <span class="required">*</span></label>
        <select name="position" class="form-control @error('position') is-invalid @enderror" required>
            <option value="">اختر الموضع</option>
            @foreach(['hero'=>'رئيسي (هيرو)','sidebar'=>'جانبي','popup'=>'نافذة منبثقة','section'=>'قسم في الصفحة'] as $val=>$label)
            <option value="{{ $val }}" {{ old('position', $banner->position ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('position')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">الرابط عند النقر</label>
        <input type="url" name="link" class="form-control" placeholder="https://..."
               value="{{ old('link', $banner->link ?? '') }}">
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">نص زر العربي</label>
        <input type="text" name="button_text_ar" class="form-control"
               value="{{ old('button_text_ar', $banner->button_text_ar ?? '') }}" placeholder="تسوق الآن">
    </div>
    <div class="form-group">
        <label class="form-label">نص زر الإنجليزي</label>
        <input type="text" name="button_text_en" class="form-control"
               value="{{ old('button_text_en', $banner->button_text_en ?? '') }}" placeholder="Shop Now">
    </div>
</div>

<div class="form-group">
    <label class="form-label">صورة البنر {{ $isEdit ? '' : '<span class="required">*</span>' }}</label>
    @if($isEdit && $banner->image)
    <div style="margin-bottom:10px">
        <img src="{{ asset('storage/'.$banner->image) }}" style="max-width:300px;max-height:120px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--border)">
    </div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*" {{ $isEdit ? '' : 'required' }}>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">تاريخ البداية</label>
        <input type="datetime-local" name="starts_at" class="form-control"
               value="{{ old('starts_at', $isEdit && $banner->starts_at ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">تاريخ الانتهاء</label>
        <input type="datetime-local" name="expires_at" class="form-control"
               value="{{ old('expires_at', $isEdit && $banner->expires_at ? $banner->expires_at->format('Y-m-d\TH:i') : '') }}">
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">الترتيب</label>
        <input type="number" name="sort_order" class="form-control" min="0"
               value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
    </div>
    <div class="form-group" style="padding-top:28px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" style="width:18px;height:18px"
                   {{ old('is_active', $isEdit ? $banner->is_active : true) ? 'checked' : '' }}>
            تفعيل البنر
        </label>
    </div>
</div>

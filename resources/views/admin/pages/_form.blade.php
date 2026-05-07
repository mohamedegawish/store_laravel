@php $isEdit = isset($page); @endphp

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">عنوان الصفحة بالعربي <span class="required">*</span></label>
        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
               value="{{ old('title_ar', $page->title_ar ?? '') }}" required>
        @error('title_ar')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">عنوان الصفحة بالإنجليزي <span class="required">*</span></label>
        <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
               value="{{ old('title_en', $page->title_en ?? '') }}" required>
        @error('title_en')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label">الرابط (Slug) <span class="required">*</span></label>
    <input type="text" name="slug" id="slugField" class="form-control @error('slug') is-invalid @enderror"
           value="{{ old('slug', $page->slug ?? '') }}" placeholder="about-us"
           {{ $isEdit ? 'readonly' : '' }}>
    <div class="form-hint">{{ $isEdit ? 'لا يمكن تغيير الرابط بعد النشر' : 'يُملأ تلقائياً من العنوان — يمكن التعديل' }}</div>
    @error('slug')<div class="form-error">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label class="form-label">المحتوى بالعربي <span class="required">*</span></label>
    <textarea name="content_ar" class="form-control @error('content_ar') is-invalid @enderror" rows="12">{{ old('content_ar', $page->content_ar ?? '') }}</textarea>
    @error('content_ar')<div class="form-error">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label class="form-label">المحتوى بالإنجليزي</label>
    <textarea name="content_en" class="form-control" rows="12">{{ old('content_en', $page->content_en ?? '') }}</textarea>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label">عنوان SEO</label>
        <input type="text" name="meta_title" class="form-control"
               value="{{ old('meta_title', $page->meta_title ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">الترتيب</label>
        <input type="number" name="sort_order" class="form-control" min="0"
               value="{{ old('sort_order', $page->sort_order ?? 0) }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label">وصف SEO</label>
    <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" style="width:18px;height:18px"
               {{ old('is_published', $isEdit ? $page->is_published : true) ? 'checked' : '' }}>
        نشر الصفحة
    </label>
</div>

@if(!$isEdit)
@push('scripts')
<script>
document.querySelector('[name="title_ar"]')?.addEventListener('input', function() {
    const field = document.getElementById('slugField');
    if (!field.dataset.touched) {
        field.value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]/g, '');
    }
});
document.getElementById('slugField')?.addEventListener('input', function() {
    this.dataset.touched = '1';
});
</script>
@endpush
@endif

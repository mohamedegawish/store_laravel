<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div class="form-group">
        <label class="form-label">الاسم بالعربي <span>*</span></label>
        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $brand?->name_ar) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">الاسم بالإنجليزي <span>*</span></label>
        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $brand?->name_en) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">الترتيب</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $brand?->sort_order ?? 0) }}" min="0">
    </div>
    <div class="form-group">
        <label class="form-label">الحالة</label>
        <select name="is_active" class="form-control form-select">
            <option value="1" {{ old('is_active', $brand?->is_active ?? 1) == 1 ? 'selected' : '' }}>نشط</option>
            <option value="0" {{ old('is_active', $brand?->is_active) == 0 ? 'selected' : '' }}>مخفي</option>
        </select>
    </div>
</div>
<div class="form-group" style="margin-bottom:20px">
    <label class="form-label">الشعار (Logo)</label>
    @if($brand?->logo)
    <div style="margin-bottom:8px"><img src="{{ asset('storage/'.$brand->logo) }}" style="height:50px;border-radius:6px;background:#f8f8f8;padding:4px"></div>
    @endif
    <input type="file" name="logo" class="form-control" accept="image/*">
</div>

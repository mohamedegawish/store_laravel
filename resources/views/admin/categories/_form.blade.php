<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div class="form-group">
        <label class="form-label">الاسم بالعربي <span>*</span></label>
        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $category?->name_ar) }}" required>
        @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">الاسم بالإنجليزي <span>*</span></label>
        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $category?->name_en) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">التصنيف الأب</label>
        <select name="parent_id" class="form-control form-select">
            <option value="">— تصنيف رئيسي —</option>
            @foreach($parents as $p)
            @if(!$category || $p->id !== $category->id)
            <option value="{{ $p->id }}" {{ old('parent_id', $category?->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name_ar }}</option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">أيقونة (Font Awesome class)</label>
        <input type="text" name="icon" class="form-control" value="{{ old('icon', $category?->icon) }}" placeholder="fas fa-tag">
    </div>
    <div class="form-group">
        <label class="form-label">الترتيب</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" min="0">
    </div>
    <div class="form-group">
        <label class="form-label">الحالة</label>
        <select name="is_active" class="form-control form-select">
            <option value="1" {{ old('is_active', $category?->is_active ?? 1) == 1 ? 'selected' : '' }}>نشط</option>
            <option value="0" {{ old('is_active', $category?->is_active) == 0 ? 'selected' : '' }}>مخفي</option>
        </select>
    </div>
</div>
<div class="form-group" style="margin-bottom:16px">
    <label class="form-label">الصورة</label>
    @if($category?->image)
    <div style="margin-bottom:8px"><img src="{{ asset('storage/'.$category->image) }}" style="height:60px;border-radius:8px"></div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
</div>
<div class="form-group" style="margin-bottom:20px">
    <label class="form-label">الوصف (عربي)</label>
    <textarea name="description_ar" class="form-control" rows="2">{{ old('description_ar', $category?->description_ar) }}</textarea>
</div>

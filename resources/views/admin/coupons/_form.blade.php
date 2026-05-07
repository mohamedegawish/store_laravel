<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div class="form-group">
        <label class="form-label">كود الخصم <span>*</span></label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $coupon?->code) }}" required style="font-family:monospace;font-size:1.1rem;text-transform:uppercase">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">الاسم (عربي)</label>
        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $coupon?->name_ar) }}">
    </div>
    <div class="form-group">
        <label class="form-label">نوع الخصم <span>*</span></label>
        <select name="type" class="form-control form-select" required>
            <option value="fixed"      {{ old('type', $coupon?->type) === 'fixed'      ? 'selected' : '' }}>مبلغ ثابت (ر.س)</option>
            <option value="percentage" {{ old('type', $coupon?->type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">القيمة <span>*</span></label>
        <input type="number" name="value" class="form-control" value="{{ old('value', $coupon?->value) }}" step="0.01" min="0" required>
        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">الحد الأدنى للطلب</label>
        <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon?->min_order_amount) }}" step="0.01" min="0" placeholder="0">
    </div>
    <div class="form-group">
        <label class="form-label">أقصى خصم (للنسبة المئوية)</label>
        <input type="number" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon?->max_discount_amount) }}" step="0.01" min="0">
    </div>
    <div class="form-group">
        <label class="form-label">حد الاستخدام الكلي</label>
        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon?->usage_limit) }}" min="0" placeholder="غير محدود">
    </div>
    <div class="form-group">
        <label class="form-label">حد لكل مستخدم</label>
        <input type="number" name="user_limit" class="form-control" value="{{ old('user_limit', $coupon?->user_limit ?? 1) }}" min="1">
    </div>
    <div class="form-group">
        <label class="form-label">تاريخ البداية</label>
        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="form-group">
        <label class="form-label">تاريخ الانتهاء</label>
        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}">
    </div>
</div>
<div class="form-group" style="margin-bottom:20px">
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
        <label class="switch">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }}>
            <span class="slider"></span>
        </label>
        <span class="form-label" style="margin:0">الكوبون نشط</span>
    </label>
</div>

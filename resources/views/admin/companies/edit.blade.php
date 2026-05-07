@extends('layouts.admin')

@section('title', 'تعديل ' . $company->company_name . ' | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.companies.index') }}">الشركات</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.companies.show', $company) }}">{{ $company->company_name }}</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تعديل</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تعديل: {{ $company->company_name }}</h1>
        <p class="page-subtitle">تحديث بيانات الشركة</p>
    </div>
    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-right"></i> رجوع
    </a>
</div>

<form action="{{ route('admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data" id="companyForm">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 22px; align-items: start;">

        {{-- Logo upload --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-image"></i> الشعار</h3></div>
            <div class="card-body">
                <div class="logo-upload-area has-file" id="logoArea" onclick="document.getElementById('logoInput').click()">
                    <img id="logoPreview" class="logo-preview"
                         src="{{ $company->logo ? asset('storage/' . $company->logo) : '' }}"
                         alt="الشعار الحالي"
                         style="{{ $company->logo ? '' : 'display:none;' }}">
                    <div id="logoPlaceholder" {{ $company->logo ? 'style=display:none' : '' }}>
                        <i class="fas fa-cloud-arrow-up" style="font-size:2rem;color:var(--primary);margin-bottom:8px;"></i>
                        <p style="font-size:0.85rem;color:var(--text-2);">انقر لتغيير الشعار</p>
                    </div>
                    <input type="file" id="logoInput" name="logo" accept="image/*" onchange="previewLogo(this)">
                </div>
                @error('logo')
                    <p class="form-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="card-body" style="border-top:1px solid var(--border);">
                <div class="form-group">
                    <label class="form-label">الحالة <span class="required">*</span></label>
                    <select name="status" class="form-control">
                        <option value="pending"  {{ old('status', $company->status) === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="active"   {{ old('status', $company->status) === 'active'   ? 'selected' : '' }}>نشطة</option>
                        <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>معطلة</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">المستخدم المرتبط</label>
                    <select name="user_id" class="form-control">
                        <option value="">— لا يوجد —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $company->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Form fields --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-circle-info"></i> المعلومات الأساسية</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">اسم الشركة <span class="required">*</span></label>
                            <input type="text" name="company_name" class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}" value="{{ old('company_name', $company->company_name) }}">
                            @error('company_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">معرف الرابط (Slug) <span class="required">*</span></label>
                            <input type="text" name="slug" class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" value="{{ old('slug', $company->slug) }}" dir="ltr">
                            @error('slug')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" name="company_email" class="form-control {{ $errors->has('company_email') ? 'is-invalid' : '' }}" value="{{ old('company_email', $company->company_email) }}" dir="ltr">
                            @error('company_email')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">رئيس مجلس الإدارة <span class="required">*</span></label>
                            <input type="text" name="chairman_name" class="form-control" value="{{ old('chairman_name', $company->chairman_name) }}">
                            @error('chairman_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">المدير التنفيذي <span class="required">*</span></label>
                            <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $company->manager_name) }}">
                            @error('manager_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">هاتف المدير <span class="required">*</span></label>
                            <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone', $company->manager_phone) }}" dir="ltr">
                            @error('manager_phone')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">عنوان المصنع <span class="required">*</span></label>
                            <input type="text" name="factory_address" class="form-control" value="{{ old('factory_address', $company->factory_address) }}">
                            @error('factory_address')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-phone"></i> بيانات التواصل</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">الموقع الإلكتروني</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}" dir="ltr">
                            @error('website')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">رابط المنتجات</label>
                            <input type="text" name="product_link" class="form-control" value="{{ old('product_link', $company->product_link) }}" dir="ltr">
                        </div>
                        <div class="form-group">
                            <label class="form-label">الخط الساخن</label>
                            <input type="text" name="hotline" class="form-control" value="{{ old('hotline', $company->hotline) }}" dir="ltr">
                        </div>
                        <div class="form-group">
                            <label class="form-label">رقم واتساب</label>
                            <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $company->whatsapp) }}" dir="ltr">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">رابط الموقع على الخريطة</label>
                            <input type="text" name="location_url" class="form-control" value="{{ old('location_url', $company->location_url) }}" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-store"></i> الفروع والمعارض وساعات العمل</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">الفروع</label>
                            <input type="text" name="branches" class="form-control" value="{{ old('branches', $company->branches) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ساعات عمل الفروع</label>
                            <input type="text" name="branches_working_hours" class="form-control" value="{{ old('branches_working_hours', $company->branches_working_hours) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">المعارض</label>
                            <input type="text" name="exhibitions" class="form-control" value="{{ old('exhibitions', $company->exhibitions) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ساعات عمل المعارض</label>
                            <input type="text" name="exhibitions_working_hours" class="form-control" value="{{ old('exhibitions_working_hours', $company->exhibitions_working_hours) }}">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">ساعات العمل الرئيسية</label>
                            <input type="text" name="company_working_hours" class="form-control" value="{{ old('company_working_hours', $company->company_working_hours) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group" style="justify-content:flex-end;">
                <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-secondary">
                    <i class="fas fa-xmark"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-floppy-disk"></i> حفظ التغييرات
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush


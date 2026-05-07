@extends('layouts.admin')

@section('title', 'إضافة شركة جديدة | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.companies.index') }}">الشركات</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">إضافة شركة</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إضافة شركة جديدة</h1>
        <p class="page-subtitle">أدخل بيانات الشركة كاملة ثم اضغط حفظ</p>
    </div>
    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-right"></i> رجوع
    </a>
</div>

<form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" id="companyForm">
    @csrf

    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 22px; align-items: start;">

        {{-- Logo upload --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-image"></i> الشعار</h3></div>
            <div class="card-body">
                <div class="logo-upload-area" id="logoArea" onclick="document.getElementById('logoInput').click()">
                    <img id="logoPreview" class="logo-preview" src="{{ asset('images/placeholder-logo.png') }}"
                         style="display:none;" alt="معاينة الشعار">
                    <div id="logoPlaceholder">
                        <i class="fas fa-cloud-arrow-up" style="font-size:2rem;color:var(--primary);margin-bottom:8px;"></i>
                        <p style="font-size:0.85rem;color:var(--text-2);margin-bottom:4px;">انقر لرفع الشعار</p>
                        <p style="font-size:0.75rem;color:var(--text-3);">JPG, PNG, WEBP — حد أقصى 2MB</p>
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
                    <select name="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}">
                        <option value="pending"  {{ old('status', 'pending') === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>نشطة</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>معطلة</option>
                    </select>
                    @error('status')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">المستخدم المرتبط</label>
                    <select name="user_id" class="form-control">
                        <option value="">— لا يوجد —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Form fields --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Basic info card --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-circle-info"></i> المعلومات الأساسية</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">اسم الشركة <span class="required">*</span></label>
                            <input type="text" name="company_name" class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}" value="{{ old('company_name') }}" placeholder="مثال: مجموعة النيل للصناعات">
                            @error('company_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">معرف الرابط (Slug) <span class="required">*</span></label>
                            <input type="text" name="slug" class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" value="{{ old('slug') }}" placeholder="مثال: nile-group" dir="ltr">
                            @error('slug')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" name="company_email" class="form-control {{ $errors->has('company_email') ? 'is-invalid' : '' }}" value="{{ old('company_email') }}" placeholder="info@company.com" dir="ltr">
                            @error('company_email')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">رئيس مجلس الإدارة <span class="required">*</span></label>
                            <input type="text" name="chairman_name" class="form-control {{ $errors->has('chairman_name') ? 'is-invalid' : '' }}" value="{{ old('chairman_name') }}" placeholder="الاسم الكامل">
                            @error('chairman_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">المدير التنفيذي <span class="required">*</span></label>
                            <input type="text" name="manager_name" class="form-control {{ $errors->has('manager_name') ? 'is-invalid' : '' }}" value="{{ old('manager_name') }}" placeholder="الاسم الكامل">
                            @error('manager_name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">هاتف المدير <span class="required">*</span></label>
                            <input type="text" name="manager_phone" class="form-control {{ $errors->has('manager_phone') ? 'is-invalid' : '' }}" value="{{ old('manager_phone') }}" placeholder="+20 1xx xxxx xxxx" dir="ltr">
                            @error('manager_phone')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">عنوان المصنع <span class="required">*</span></label>
                            <input type="text" name="factory_address" class="form-control {{ $errors->has('factory_address') ? 'is-invalid' : '' }}" value="{{ old('factory_address') }}" placeholder="المدينة، الشارع...">
                            @error('factory_address')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact info --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-phone"></i> بيانات التواصل</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">الموقع الإلكتروني</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://company.com" dir="ltr">
                            @error('website')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">رابط المنتجات</label>
                            <input type="text" name="product_link" class="form-control" value="{{ old('product_link') }}" placeholder="رابط كتالوج المنتجات" dir="ltr">
                            @error('product_link')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">الخط الساخن</label>
                            <input type="text" name="hotline" class="form-control" value="{{ old('hotline') }}" placeholder="08008880xx" dir="ltr">
                            @error('hotline')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">رقم واتساب</label>
                            <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="+20 1xx xxxx xxxx" dir="ltr">
                            @error('whatsapp')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">رابط الموقع على الخريطة</label>
                            <input type="text" name="location_url" class="form-control" value="{{ old('location_url') }}" placeholder="https://maps.google.com/..." dir="ltr">
                            @error('location_url')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Branches, exhibitions, hours --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-store"></i> الفروع والمعارض وساعات العمل</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">الفروع</label>
                            <input type="text" name="branches" class="form-control" value="{{ old('branches') }}" placeholder="عدد الفروع أو أماكنها">
                            @error('branches')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">ساعات عمل الفروع</label>
                            <input type="text" name="branches_working_hours" class="form-control" value="{{ old('branches_working_hours') }}" placeholder="9 ص – 5 م">
                            @error('branches_working_hours')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">المعارض</label>
                            <input type="text" name="exhibitions" class="form-control" value="{{ old('exhibitions') }}" placeholder="عدد المعارض أو أماكنها">
                            @error('exhibitions')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">ساعات عمل المعارض</label>
                            <input type="text" name="exhibitions_working_hours" class="form-control" value="{{ old('exhibitions_working_hours') }}" placeholder="10 ص – 10 م">
                            @error('exhibitions_working_hours')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">ساعات العمل الرئيسية</label>
                            <input type="text" name="company_working_hours" class="form-control" value="{{ old('company_working_hours') }}" placeholder="السبت–الخميس: 9 ص – 5 م">
                            @error('company_working_hours')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="btn-group" style="justify-content:flex-end;">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-xmark"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-floppy-disk"></i> حفظ الشركة
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
            document.getElementById('logoArea').classList.add('has-file');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush


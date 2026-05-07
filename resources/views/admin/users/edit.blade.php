@extends('layouts.admin')

@section('title', 'تعديل ' . $user->name . ' | المدير العام')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.users.index') }}">المستخدمون</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">تعديل</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">تعديل: {{ $user->name }}</h1>
        <p class="page-subtitle">تحديث بيانات الحساب</p>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-right"></i> رجوع
    </a>
</div>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div style="max-width: 800px;">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-user-pen"></i> بيانات الحساب</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $user->name) }}">
                        @error('name')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email', $user->email) }}" dir="ltr">
                        @error('email')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="اتركه فارغاً للإبقاء على الحالية" dir="ltr">
                        <p class="form-hint">اتركه فارغاً إذا لم تريد تغيير كلمة المرور</p>
                        @error('password')<p class="form-error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور الجديدة" dir="ltr">
                    </div>
                    <div class="form-group">
                        <label class="form-label">الدور <span class="required">*</span></label>
                        <select name="role" class="form-control">
                            <option value="user"          {{ old('role', $user->role) === 'user'          ? 'selected' : '' }}>مستخدم عادي</option>
                            <option value="company_admin" {{ old('role', $user->role) === 'company_admin' ? 'selected' : '' }}>مدير شركة</option>
                            <option value="super_admin"   {{ old('role', $user->role) === 'super_admin'   ? 'selected' : '' }}>مدير عام</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">الشركة المرتبطة</label>
                        <select name="company_id" class="form-control">
                            <option value="">— لا يوجد —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer" style="display:flex; justify-content:flex-end; gap:10px;">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary"><i class="fas fa-xmark"></i> إلغاء</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> حفظ التغييرات</button>
            </div>
        </div>
    </div>
</form>

@endsection


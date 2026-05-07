@extends('layouts.auth')
@section('title', 'إنشاء حساب')
@section('content')

<h1>إنشاء حساب جديد</h1>
<p class="auth-subtitle">انضم إلينا وابدأ التسوق الآن</p>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group">
        <label for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}"
               class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required autofocus>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password"
               class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">تأكيد كلمة المرور</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn-auth"><i class="fas fa-user-plus"></i> إنشاء الحساب</button>
</form>

<div class="auth-links">
    لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
</div>
@endsection

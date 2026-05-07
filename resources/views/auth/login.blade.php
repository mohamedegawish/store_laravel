@extends('layouts.auth')
@section('title', 'تسجيل الدخول')
@section('content')

<h1>تسجيل الدخول</h1>
<p class="auth-subtitle">أهلاً بعودتك! سجّل دخولك للمتابعة</p>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password"
               class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer">
            <input type="checkbox" name="remember" style="width:16px;height:16px">
            تذكرني
        </label>
        <a href="{{ route('password.request') }}" style="font-size:.84rem;color:#6c3fc5">نسيت كلمة المرور؟</a>
    </div>
    <button type="submit" class="btn-auth"><i class="fas fa-sign-in-alt"></i> دخول</button>
</form>

<div class="auth-links">
    ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
</div>
@endsection

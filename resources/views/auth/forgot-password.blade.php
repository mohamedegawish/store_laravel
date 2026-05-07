@extends('layouts.auth')
@section('title', 'استعادة كلمة المرور')
@section('content')

<h1>نسيت كلمة المرور؟</h1>
<p class="auth-subtitle">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn-auth"><i class="fas fa-paper-plane"></i> إرسال رابط الاستعادة</button>
</form>

<div class="auth-links">
    <a href="{{ route('login') }}"><i class="fas fa-arrow-right"></i> العودة لتسجيل الدخول</a>
</div>
@endsection

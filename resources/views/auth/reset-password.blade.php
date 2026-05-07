@extends('layouts.auth')
@section('title', 'إعادة تعيين كلمة المرور')
@section('content')

<h1>إعادة تعيين كلمة المرور</h1>
<p class="auth-subtitle">أدخل كلمة المرور الجديدة</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
               class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="password">كلمة المرور الجديدة</label>
        <input type="password" id="password" name="password"
               class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">تأكيد كلمة المرور</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn-auth"><i class="fas fa-lock"></i> تعيين كلمة المرور</button>
</form>
@endsection

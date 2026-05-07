@extends('layouts.app')

@section('title', 'تسجيل الدخول - سوق')

@push('styles')
<style>
    .auth-container { max-width: 400px; margin: 4rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid var(--border-color); }
    .auth-container h2 { text-align: center; margin-bottom: 2rem; color: var(--primary-color); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; }
    .btn-submit { width: 100%; padding: 0.75rem; background: var(--primary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; }
    .auth-links { text-align: center; margin-top: 1.5rem; }
    .auth-links a { color: var(--primary-color); text-decoration: underline; }
</style>
@endpush

@section('content')
<main class="container">
    <div class="auth-container">
        <h2>تسجيل الدخول</h2>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="أدخل بريدك الإلكتروني" required>
                @error('email') <span style="color:red; font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="أدخل كلمة المرور" required>
                @error('password') <span style="color:red; font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>
            <div style="text-align: left; margin-bottom: 1rem;">
                <a href="{{ route('password.request') }}" style="color:red; font-size:0.9rem; text-decoration:none;">نسيت كلمة المرور؟</a>
            </div>
            <button type="submit" class="btn-submit">دخول</button>
        </form>
        <div class="auth-links">
            <p>ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
        </div>
    </div>
</main>
@endsection



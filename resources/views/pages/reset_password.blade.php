@extends('layouts.app')

@section('title', 'تعيين كلمة مرور جديدة - سوق')

@push('styles')
<style>
    .auth-container { max-width: 400px; margin: 4rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid var(--border-color); }
    .auth-container h2 { text-align: center; margin-bottom: 2rem; color: var(--primary-color); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; }
    .btn-submit { width: 100%; padding: 0.75rem; background: var(--primary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; }
</style>
@endpush

@section('content')
<main class="container">
    <div class="auth-container">
        <h2>تعيين كلمة مرور جديدة</h2>
        
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required>
                @error('email') <span style="color:red; font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" placeholder="6 أحرف على الأقل" required>
                @error('password') <span style="color:red; font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" placeholder="أعد إدخال كلمة المرور" required>
            </div>
            
            <button type="submit" class="btn-submit">تغيير كلمة المرور</button>
        </form>
    </div>
</main>
@endsection



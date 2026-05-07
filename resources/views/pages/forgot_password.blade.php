@extends('layouts.app')

@section('title', 'نسيت كلمة المرور - سوق')

@push('styles')
<style>
    .auth-container { max-width: 400px; margin: 4rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid var(--border-color); }
    .auth-container h2 { text-align: center; margin-bottom: 1rem; color: var(--primary-color); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; }
    .btn-submit { width: 100%; padding: 0.75rem; background: var(--primary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; }
</style>
@endpush

@section('content')
<main class="container">
    <div class="auth-container">
        <h2>استعادة كلمة المرور</h2>
        <p style="margin-bottom: 1.5rem; text-align: center; color: var(--text-light);">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.</p>
        
        @if (session('status'))
            <div style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="أدخل بريدك الإلكتروني" required>
                @error('email') <span style="color:red; font-size: 0.9rem;">{{ $message }}</span> @enderror
            </div>
            
            <button type="submit" class="btn-submit">إرسال رابط الاستعادة</button>
        </form>
    </div>
</main>
@endsection



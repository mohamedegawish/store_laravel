@extends('store.account.layout')
@section('title', 'الملف الشخصي')

@section('account_content')

@if(session('success'))
<div style="background:#D1FAE5;border:1px solid #A7F3D0;border-radius:var(--radius);padding:14px 18px;margin-bottom:20px;color:#065F46;font-size:.88rem">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Profile Info --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
        <h3 style="font-weight:700;font-size:1rem;margin-bottom:20px"><i class="fas fa-user-edit" style="color:var(--primary);margin-inline-end:8px"></i> المعلومات الشخصية</h3>
        <form method="POST" action="{{ route('store.account.profile.update') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">الاسم</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           style="width:100%;height:42px;border:1.5px solid {{ $errors->has('name') ? 'var(--danger)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    @error('name')<div style="color:var(--danger);font-size:.78rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           style="width:100%;height:42px;border:1.5px solid {{ $errors->has('email') ? 'var(--danger)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    @error('email')<div style="color:var(--danger);font-size:.78rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">رقم الهاتف</label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                           style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                </div>
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth?->format('Y-m-d')) }}"
                           style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px">حفظ التغييرات</button>
        </form>
    </div>

    {{-- Change Password --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
        <h3 style="font-weight:700;font-size:1rem;margin-bottom:20px"><i class="fas fa-lock" style="color:var(--warning);margin-inline-end:8px"></i> تغيير كلمة المرور</h3>
        <form method="POST" action="{{ route('store.account.password.update') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr;gap:14px;max-width:400px">
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required
                           style="width:100%;height:42px;border:1.5px solid {{ $errors->has('current_password') ? 'var(--danger)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    @error('current_password')<div style="color:var(--danger);font-size:.78rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">كلمة المرور الجديدة</label>
                    <input type="password" name="password" required
                           style="width:100%;height:42px;border:1.5px solid {{ $errors->has('password') ? 'var(--danger)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    @error('password')<div style="color:var(--danger);font-size:.78rem;margin-top:4px">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                           style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                </div>
            </div>
            <button type="submit" class="btn btn-outline" style="margin-top:16px">تغيير كلمة المرور</button>
        </form>
    </div>

</div>
@endsection

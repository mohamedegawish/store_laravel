@extends('layouts.store')
@section('title', 'اتصل بنا')
@section('content')

<div style="background:var(--surface-2);padding:48px 0 32px">
    <div class="container">
        <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px">اتصل بنا</h1>
        <p style="color:var(--text-muted)">نحن هنا لمساعدتك في أي وقت</p>
    </div>
</div>

<div class="container" style="padding:48px 0">
    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:48px;align-items:start">

        {{-- Contact info --}}
        <div>
            <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:24px">معلومات التواصل</h2>

            @if($settings->contact_email ?? false)
            <div style="display:flex;gap:14px;align-items:flex-start;margin-bottom:20px">
                <div style="width:44px;height:44px;background:var(--primary-soft);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <div style="font-weight:600;margin-bottom:4px">البريد الإلكتروني</div>
                    <a href="mailto:{{ $settings->contact_email }}" style="color:var(--text-muted);text-decoration:none">{{ $settings->contact_email }}</a>
                </div>
            </div>
            @endif

            @if($settings->contact_phone ?? false)
            <div style="display:flex;gap:14px;align-items:flex-start;margin-bottom:20px">
                <div style="width:44px;height:44px;background:var(--primary-soft);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0">
                    <i class="fas fa-phone"></i>
                </div>
                <div>
                    <div style="font-weight:600;margin-bottom:4px">رقم الهاتف</div>
                    <a href="tel:{{ $settings->contact_phone }}" style="color:var(--text-muted);text-decoration:none">{{ $settings->contact_phone }}</a>
                </div>
            </div>
            @endif

            @if($settings->address ?? false)
            <div style="display:flex;gap:14px;align-items:flex-start;margin-bottom:20px">
                <div style="width:44px;height:44px;background:var(--primary-soft);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0">
                    <i class="fas fa-location-dot"></i>
                </div>
                <div>
                    <div style="font-weight:600;margin-bottom:4px">العنوان</div>
                    <span style="color:var(--text-muted)">{{ $settings->address }}</span>
                </div>
            </div>
            @endif

            {{-- Social links --}}
            @if($settings->social_twitter || $settings->social_instagram || $settings->social_facebook)
            <div style="margin-top:32px">
                <div style="font-weight:700;margin-bottom:14px">تابعنا على</div>
                <div style="display:flex;gap:10px">
                    @if($settings->social_twitter)
                    <a href="{{ $settings->social_twitter }}" target="_blank" style="width:40px;height:40px;background:var(--surface-2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);transition:.2s;border:1px solid var(--border)" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"><i class="fab fa-x-twitter"></i></a>
                    @endif
                    @if($settings->social_instagram)
                    <a href="{{ $settings->social_instagram }}" target="_blank" style="width:40px;height:40px;background:var(--surface-2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);transition:.2s;border:1px solid var(--border)" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if($settings->social_facebook)
                    <a href="{{ $settings->social_facebook }}" target="_blank" style="width:40px;height:40px;background:var(--surface-2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);transition:.2s;border:1px solid var(--border)" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"><i class="fab fa-facebook-f"></i></a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Contact form --}}
        <div style="background:var(--surface);border-radius:16px;padding:32px;border:1px solid var(--border)">
            <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:24px">أرسل لنا رسالة</h2>

            @if(session('success'))
            <div style="background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:14px 18px;border-radius:10px;margin-bottom:20px">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('store.contact.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                    <div>
                        <label style="display:block;font-size:.84rem;font-weight:600;color:var(--text-2);margin-bottom:7px">الاسم <span style="color:red">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:.9rem;color:var(--text);outline:none;background:var(--surface)" placeholder="اسمك الكامل">
                        @error('name')<span style="font-size:.78rem;color:red">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:.84rem;font-weight:600;color:var(--text-2);margin-bottom:7px">البريد الإلكتروني <span style="color:red">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:.9rem;color:var(--text);outline:none;background:var(--surface)" placeholder="email@example.com">
                        @error('email')<span style="font-size:.78rem;color:red">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:.84rem;font-weight:600;color:var(--text-2);margin-bottom:7px">رقم الهاتف</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:.9rem;color:var(--text);outline:none;background:var(--surface)" placeholder="+966 5x xxx xxxx">
                </div>
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:.84rem;font-weight:600;color:var(--text-2);margin-bottom:7px">الموضوع</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:.9rem;color:var(--text);outline:none;background:var(--surface)" placeholder="موضوع رسالتك">
                </div>
                <div style="margin-bottom:24px">
                    <label style="display:block;font-size:.84rem;font-weight:600;color:var(--text-2);margin-bottom:7px">الرسالة <span style="color:red">*</span></label>
                    <textarea name="message" rows="5" required
                              style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:.9rem;color:var(--text);outline:none;resize:vertical;background:var(--surface)" placeholder="اكتب رسالتك هنا...">{{ old('message') }}</textarea>
                    @error('message')<span style="font-size:.78rem;color:red">{{ $message }}</span>@enderror
                </div>
                <button type="submit" style="width:100%;padding:13px;background:var(--primary);color:white;border:none;border-radius:10px;font-family:inherit;font-size:.95rem;font-weight:700;cursor:pointer">
                    <i class="fas fa-paper-plane"></i> إرسال الرسالة
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
@media (max-width:768px) {
    .contact-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush
@endsection

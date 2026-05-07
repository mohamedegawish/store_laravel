@extends('layouts.store')

@section('content')
<div class="container" style="padding-top:28px;padding-bottom:60px">

    <nav class="breadcrumb">
        <a href="{{ route('store.home') }}">الرئيسية</a>
        <span class="sep">/</span>
        <span>حسابي</span>
    </nav>

    <div style="display:grid;grid-template-columns:240px 1fr;gap:28px;align-items:start" id="accountLayout">

        {{-- Sidebar --}}
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
            <div style="padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:46px;height:46px;border-radius:50%;object-fit:cover">
                <div>
                    <div style="font-weight:700;font-size:.92rem">{{ auth()->user()->name }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted)">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <nav>
                @foreach([
                    ['store.account.dashboard','fas fa-tachometer-alt','لوحة التحكم'],
                    ['store.account.orders','fas fa-box','طلباتي'],
                    ['store.account.wishlist','fas fa-heart','المفضلة'],
                    ['store.account.profile','fas fa-user-edit','الملف الشخصي'],
                    ['store.account.addresses','fas fa-map-marker-alt','عناويني'],
                ] as [$route,$icon,$label])
                <a href="{{ route($route) }}"
                   style="display:flex;align-items:center;gap:12px;padding:13px 20px;font-size:.88rem;font-weight:500;border-bottom:1px solid var(--border);color:{{ request()->routeIs($route) ? 'var(--primary)' : 'var(--text)' }};background:{{ request()->routeIs($route) ? 'var(--primary-light)' : '#fff' }};transition:all .15s">
                    <i class="{{ $icon }}" style="width:18px;color:{{ request()->routeIs($route) ? 'var(--primary)' : 'var(--text-muted)' }}"></i>
                    {{ $label }}
                    @if(request()->routeIs($route))<i class="fas fa-chevron-left" style="margin-inline-start:auto;font-size:.7rem;color:var(--primary)"></i>@endif
                </a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width:100%;display:flex;align-items:center;gap:12px;padding:13px 20px;font-size:.88rem;font-weight:500;color:var(--danger);background:#fff;border:none;cursor:pointer;font-family:var(--font)">
                        <i class="fas fa-sign-out-alt" style="width:18px"></i> تسجيل الخروج
                    </button>
                </form>
            </nav>
        </div>

        {{-- Content --}}
        <div>
            @yield('account_content')
        </div>
    </div>
</div>

@push('styles')
<style>
@media(max-width:768px){
    #accountLayout { grid-template-columns:1fr!important; }
}
</style>
@endpush
@endsection

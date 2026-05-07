<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'تسجيل الدخول') - {{ $settings->store_name_ar ?? 'المتجر' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: {{ $settings->primary_color ?? '#6C3FC5' }};
            --primary-soft: rgba(108, 63, 197, 0.1);
            --font: '{{ $settings->font_family ?? 'Cairo' }}', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font);
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo a {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
        }
        .auth-logo small { display: block; font-size: .8rem; color: #94a3b8; margin-top: 4px; }
        h1 { font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-bottom: 6px; text-align: center; }
        .auth-subtitle { font-size: .88rem; color: #64748b; text-align: center; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: .84rem; font-weight: 600; color: #475569; margin-bottom: 7px; }
        input[type=email], input[type=password], input[type=text] {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: var(--font);
            font-size: .9rem;
            color: #1e293b;
            outline: none;
            transition: border-color .2s;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
        .btn-auth {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .2s;
            margin-top: 8px;
        }
        .btn-auth:hover { opacity: .9; transform: translateY(-1px); }
        .auth-links { text-align: center; margin-top: 20px; font-size: .85rem; color: #64748b; }
        .auth-links a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .alert-danger { background: #fef2f2; border: 1px solid #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 10px; font-size: .86rem; margin-bottom: 18px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px 16px; border-radius: 10px; font-size: .86rem; margin-bottom: 18px; }
        .is-invalid { border-color: #ef4444 !important; }
        .invalid-feedback { font-size: .78rem; color: #ef4444; margin-top: 5px; }
        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
        .divider span { font-size: .8rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('store.home') }}">{{ $settings->store_name_ar ?? 'المتجر' }}</a>
            <small>{{ $settings->store_name_en ?? 'Store' }}</small>
        </div>

        @if(session('error'))
        <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if(session('status'))
        <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('status') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>

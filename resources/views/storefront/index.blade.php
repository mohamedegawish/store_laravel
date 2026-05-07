<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة المتجر المتعدد | سوق</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4A3AFF;
            --primary-dark: #3a2bdf;
            --text: #1a1a1a;
            --text-light: #666;
            --bg: #F8F9FA;
            --surface: #ffffff;
            --border: #e2e8f0;
            --radius-lg: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Tajawal', sans-serif; }
        body { background-color: var(--bg); color: var(--text); line-height: 1.6; }
        
        .navbar { background: var(--surface); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: sticky; top:0; z-index:100; }
        .logo { font-size: 1.8rem; font-weight: 900; color: var(--primary); text-decoration: none; }
        
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .btn { padding: 10px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: 0.3s; cursor: pointer; }
        .btn-outline { border: 2px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: var(--primary); color: #fff; }
        .btn-primary { background: var(--primary); color: #fff; border: 2px solid var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); }
        
        .hero { text-align: center; padding: 100px 20px; background: linear-gradient(135deg, rgba(74,58,255,0.05) 0%, rgba(74,58,255,0.1) 100%); }
        .hero h1 { font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; color: var(--text); }
        .hero p { font-size: 1.2rem; color: var(--text-light); max-width: 600px; margin: 0 auto; line-height: 1.8; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .section-title { font-size: 2rem; font-weight: 800; margin-bottom: 40px; text-align: center; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .store-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 30px; text-align: center; transition: 0.3s; text-decoration: none; color: inherit; display: block; }
        .store-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-color: var(--primary); }
        
        .store-logo { width: 80px; height: 80px; background: var(--bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; font-weight: 800; color: var(--primary); border: 2px solid var(--border); overflow: hidden; }
        .store-name { font-size: 1.4rem; font-weight: 800; margin-bottom: 10px; }
        .store-desc { font-size: 1rem; color: var(--text-light); margin-bottom: 20px; }
        .store-link { color: var(--primary); font-weight: 700; display: inline-flex; align-items: center; gap: 8px; }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('front.index') }}" class="logo">سوق.كوم</a>
        <div class="nav-links">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">لوحة التحكم</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">دخول</a>
                <a href="{{ route('register') }}" class="btn btn-primary">حساب جديد</a>
            @endauth
        </div>
    </nav>

    <header class="hero">
        <h1>مرحباً بك في الوجهة الأولى للتسوق</h1>
        <p>اكتشف أفضل المتاجر المحلية والعالمية، تسوق بآمان وبكل سهولة. ابحث عن متاجرك المفضلة بالأسفل واكتشف أحدث العروض.</p>
    </header>

    <div class="container">
        <h2 class="section-title">المتاجر المميزة</h2>
        
        <div class="grid">
            @forelse($companies as $company)
            <a href="{{ route('front.home', $company->slug) }}" class="store-card">
                <div class="store-logo">
                    @if($company->storeSetting?->logo)
                        <img src="{{ asset('storage/'.$company->storeSetting->logo) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ mb_substr($company->name, 0, 1) }}
                    @endif
                </div>
                <h3 class="store-name">{{ $company->name }}</h3>
                <p class="store-desc">{{ Str::limit($company->description ?? 'شاهد أحدث المنتجات من متجر '.$company->name, 80) }}</p>
                <div class="store-link">
                    زيارة المتجر <i class="fas fa-arrow-left"></i>
                </div>
            </a>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: var(--surface); border-radius: var(--radius-lg); border: 1px dashed var(--border);">
                <i class="fas fa-store-slash" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                <h3>لا توجد متاجر نشطة حالياً</h3>
                <p style="color: var(--text-light);">سيتم إضافة المتاجر قريباً.</p>
            </div>
            @endforelse
        </div>
    </div>

    <footer style="text-align: center; padding: 40px; background: var(--surface); border-top: 1px solid var(--border); margin-top: 40px;">
        <p style="font-weight: 600; color: var(--text-light);">جميع الحقوق محفوظة &copy; {{ date('Y') }} لمنصة سوق المتعددة.</p>
    </footer>

</body>
</html>

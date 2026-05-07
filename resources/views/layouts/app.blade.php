<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'سوق | متجر إلكتروني عصري')</title>

    <!-- Favicons -->
    <meta name="theme-color" content="#4A3AFF">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('front/assets/css/style.css') }}">
    
    @stack('styles')
</head>
<body class="">

    <a href="#main-content" class="sr-only">تخطي إلى المحتوى الرئيسي</a>

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    @stack('scripts')
</body>
</html>



<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم المدير العام')</title>
    <meta name="description" content="@yield('meta_description', 'لوحة تحكم المدير العام لإدارة الشركات والمستخدمين')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ===== CSS TOKENS ===== */
        :root {
            --font: 'Cairo', sans-serif;

            /* Brand */
            --primary: #6C3FC5;
            --primary-light: #8B5CF6;
            --primary-dark: #5B21B6;
            --primary-soft: rgba(108, 63, 197, 0.12);
            --primary-glow: rgba(108, 63, 197, 0.35);

            --accent: #10B981;
            --accent-soft: rgba(16, 185, 129, 0.12);

            --warning: #F59E0B;
            --warning-soft: rgba(245, 158, 11, 0.12);

            --danger: #EF4444;
            --danger-soft: rgba(239, 68, 68, 0.12);

            --info: #3B82F6;
            --info-soft: rgba(59, 130, 246, 0.12);

            /* Layout */
            --sidebar-width: 270px;
            --sidebar-collapsed-width: 72px;
            --topbar-height: 64px;

            /* Light theme */
            --bg: #F1F5F9;
            --bg-2: #E8EDF5;
            --surface: #FFFFFF;
            --surface-2: #F8FAFC;
            --border: #E2E8F0;
            --border-light: #F1F5F9;
            --text: #1E293B;
            --text-2: #475569;
            --text-3: #94A3B8;
            --sidebar-bg: #0F172A;
            --sidebar-text: #CBD5E1;
            --sidebar-text-active: #FFFFFF;
            --sidebar-item-hover: rgba(255,255,255,0.07);
            --sidebar-item-active: var(--primary);

            /* Effects */
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 4px 16px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.15);
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            --bg: #0D1117;
            --bg-2: #161B22;
            --surface: #1C2333;
            --surface-2: #21262D;
            --border: #30363D;
            --border-light: #21262D;
            --text: #E6EDF3;
            --text-2: #8B949E;
            --text-3: #484F58;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
            --shadow: 0 4px 16px rgba(0,0,0,0.4);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.5);
        }

        /* ===== RESET ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
            line-height: 1.6;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: -4px 0 24px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            min-height: var(--topbar-height);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px var(--primary-glow);
        }
        .sidebar-brand-text {
            color: white;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            transition: var(--transition);
            white-space: nowrap;
        }
        .sidebar-brand-text small {
            display: block;
            font-size: 0.7rem;
            color: var(--sidebar-text);
            font-weight: 400;
            margin-top: 1px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 12px 22px 6px;
            transition: var(--transition);
            white-space: nowrap;
        }
        .sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            margin: 2px 10px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            white-space: nowrap;
        }
        .nav-item:hover {
            background: var(--sidebar-item-hover);
            color: white;
        }
        .nav-item.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 4px 12px var(--primary-glow);
        }
        .nav-item .nav-icon {
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 0.95rem;
        }
        .nav-item .nav-label { transition: var(--transition); }
        .sidebar.collapsed .nav-label { opacity: 0; width: 0; overflow: hidden; }
        .sidebar.collapsed .nav-item { justify-content: center; padding: 10px; margin: 2px 8px; }
        .sidebar.collapsed .nav-icon { width: 24px; height: 24px; font-size: 1.1rem; }

        .nav-badge {
            margin-right: auto;
            margin-left: 0;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
            transition: var(--transition);
        }
        .sidebar.collapsed .nav-badge { display: none; }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        .sidebar-user:hover { background: var(--sidebar-item-hover); }
        .sidebar-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.85rem; font-weight: 700;
            flex-shrink: 0;
        }
        .sidebar-user-info { transition: var(--transition); }
        .sidebar.collapsed .sidebar-user-info { opacity: 0; width: 0; overflow: hidden; }
        .sidebar-user-name { color: white; font-size: 0.85rem; font-weight: 600; }
        .sidebar-user-role { color: var(--sidebar-text); font-size: 0.72rem; }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0; left: 0;
            right: var(--sidebar-width);
            height: var(--topbar-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px;
            z-index: 900;
            transition: right 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }
        .sidebar.collapsed ~ .topbar { right: var(--sidebar-collapsed-width); }

        .topbar-toggle {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: none;
            background: var(--surface-2);
            color: var(--text-2);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
        }
        .topbar-toggle:hover { background: var(--primary-soft); color: var(--primary); }

        .topbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: var(--text-3);
            flex: 1;
        }
        .topbar-breadcrumb a { color: var(--text-3); text-decoration: none; transition: var(--transition); }
        .topbar-breadcrumb a:hover { color: var(--primary); }
        .topbar-breadcrumb .sep { font-size: 0.7rem; }
        .topbar-breadcrumb .current { color: var(--text); font-weight: 600; }

        .topbar-search {
            position: relative;
        }
        .topbar-search input {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 8px 16px 8px 36px;
            font-family: var(--font);
            font-size: 0.85rem;
            color: var(--text);
            width: 240px;
            transition: var(--transition);
            outline: none;
        }
        .topbar-search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
            width: 300px;
        }
        .topbar-search input::placeholder { color: var(--text-3); }
        .topbar-search .search-icon {
            position: absolute;
            left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-3);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .topbar-btn {
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border: none;
            background: transparent;
            color: var(--text-2);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            position: relative;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--primary-soft); color: var(--primary); }
        .topbar-btn .notif-dot {
            position: absolute;
            top: 6px; left: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--surface);
        }

        .topbar-divider { width: 1px; height: 24px; background: var(--border); }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        .topbar-user:hover { background: var(--surface-2); }
        .topbar-user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.8rem; font-weight: 700;
        }
        .topbar-user-name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
        .topbar-user-role { font-size: 0.7rem; color: var(--text-3); }
        .topbar-user-dropdown {
            position: absolute;
            top: calc(100% + 8px); left: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            min-width: 180px;
            padding: 6px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: var(--transition);
            z-index: 1000;
        }
        .topbar-user:hover .topbar-user-dropdown,
        .topbar-user.open .topbar-user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-2);
            text-decoration: none;
            font-size: 0.85rem;
            transition: var(--transition);
        }
        .dropdown-item:hover { background: var(--surface-2); color: var(--text); }
        .dropdown-item.danger:hover { background: var(--danger-soft); color: var(--danger); }
        .dropdown-item i { width: 16px; text-align: center; }
        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-right: var(--sidebar-width);
            margin-top: var(--topbar-height);
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-right 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 28px;
        }
        .sidebar.collapsed ~ .main-wrapper { margin-right: var(--sidebar-collapsed-width); }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }
        .page-subtitle {
            font-size: 0.85rem;
            color: var(--text-3);
            margin-top: 2px;
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 60px; height: 60px;
            border-radius: 0 var(--radius-lg) 0 60px;
            opacity: 0.08;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            border-color: transparent;
        }
        .stat-card.primary::before { background: var(--primary); }
        .stat-card.accent::before { background: var(--accent); }
        .stat-card.warning::before { background: var(--warning); }
        .stat-card.danger::before { background: var(--danger); }
        .stat-card.info::before { background: var(--info); }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .stat-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-2);
        }
        .stat-icon {
            width: 42px; height: 42px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
        }
        .stat-icon.primary { background: var(--primary-soft); color: var(--primary); }
        .stat-icon.accent { background: var(--accent-soft); color: var(--accent); }
        .stat-icon.warning { background: var(--warning-soft); color: var(--warning); }
        .stat-icon.danger { background: var(--danger-soft); color: var(--danger); }
        .stat-icon.info { background: var(--info-soft); color: var(--info); }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
            margin-bottom: 6px;
        }
        .stat-trend {
            font-size: 0.78rem;
            color: var(--text-3);
        }

        /* ===== CARDS ===== */
        .card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
        }
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-title i { color: var(--primary); }
        .card-body { padding: 22px; }
        .card-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            background: var(--surface-2);
        }

        /* ===== TABLES ===== */
        .table-responsive, .table-wrap { overflow-x: auto; }
        .table-wrap table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .table-wrap th {
            background: var(--surface-2); color: var(--text-2); font-weight: 700;
            font-size: 0.78rem; letter-spacing: 0.03em; padding: 12px 16px;
            text-align: right; border-bottom: 2px solid var(--border); white-space: nowrap;
        }
        .table-wrap td {
            padding: 14px 16px; border-bottom: 1px solid var(--border-light);
            color: var(--text); vertical-align: middle;
        }
        .table-wrap tbody tr { transition: var(--transition); }
        .table-wrap tbody tr:hover { background: var(--surface-2); }
        .table-wrap tbody tr:last-child td { border-bottom: none; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--text-2); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .table th {
            background: var(--surface-2);
            color: var(--text-2);
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
            padding: 12px 16px;
            text-align: right;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-light);
            color: var(--text);
            vertical-align: middle;
        }
        .table tbody tr { transition: var(--transition); }
        .table tbody tr:hover { background: var(--surface-2); }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-success { background: var(--accent-soft); color: var(--accent); }
        .badge-danger  { background: var(--danger-soft);  color: var(--danger); }
        .badge-warning { background: var(--warning-soft); color: var(--warning); }
        .badge-info    { background: var(--info-soft);    color: var(--info); }
        .badge-primary { background: var(--primary-soft); color: var(--primary); }
        .badge-secondary { background: var(--surface-2); color: var(--text-3); }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-sm { padding: 5px 12px; font-size: 0.8rem; }
        .btn-xs { padding: 3px 9px; font-size: 0.75rem; }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 12px var(--primary-glow); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-secondary { background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); color: var(--text); }
        .btn-success { background: var(--accent); color: white; }
        .btn-success:hover { opacity: 0.85; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { opacity: 0.85; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-outline-primary { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-outline-primary:hover { background: var(--primary-soft); }
        .btn-icon { padding: 7px; width: 34px; height: 34px; justify-content: center; }

        .btn-group { display: flex; gap: 6px; flex-wrap: wrap; }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 7px;
        }
        .form-label .required { color: var(--danger); margin-right: 3px; }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 0.875rem;
            color: var(--text);
            transition: var(--transition);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }
        .form-control::placeholder { color: var(--text-3); }
        .form-control.is-invalid { border-color: var(--danger); }
        .form-error { font-size: 0.78rem; color: var(--danger); margin-top: 5px; display: flex; align-items: center; gap: 4px; }
        .form-hint { font-size: 0.78rem; color: var(--text-3); margin-top: 5px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0 20px; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23adb5bd' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: left 12px center; background-size: 14px; padding-left: 36px; }

        /* ===== ALERTS ===== */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        .alert-success { background: var(--accent-soft); color: var(--accent); border-color: rgba(16,185,129,0.2); }
        .alert-danger  { background: var(--danger-soft);  color: var(--danger);  border-color: rgba(239,68,68,0.2); }
        .alert-warning { background: var(--warning-soft); color: var(--warning); border-color: rgba(245,158,11,0.2); }
        .alert-info    { background: var(--info-soft);    color: var(--info);    border-color: rgba(59,130,246,0.2); }
        .alert i { margin-top: 1px; flex-shrink: 0; }
        .alert-close { margin-right: auto; margin-left: 0; background: none; border: none; cursor: pointer; color: inherit; opacity: 0.6; font-size: 1rem; }
        .alert-close:hover { opacity: 1; }

        /* ===== SEARCH & FILTER BAR ===== */
        .filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            padding: 16px;
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
        }
        .filter-bar .form-control { width: auto; min-width: 160px; padding: 8px 12px; font-size: 0.85rem; }
        .filter-bar .search-wrap { position: relative; }
        .filter-bar .search-wrap .form-control { padding-right: 36px; min-width: 240px; }
        .filter-bar .search-wrap i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-3); font-size: 0.85rem; }

        /* ===== VIEW TOGGLE ===== */
        .view-toggle {
            display: flex;
            gap: 4px;
            background: var(--surface-2);
            padding: 4px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
        }
        .view-btn {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: transparent;
            color: var(--text-3);
            border-radius: 6px;
            cursor: pointer; transition: var(--transition);
            font-size: 0.9rem;
        }
        .view-btn.active { background: var(--primary); color: white; }

        /* ===== CARD GRID VIEW ===== */
        .company-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; padding: 20px; }
        .company-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 20px;
            transition: var(--transition);
            display: flex; flex-direction: column; gap: 14px;
        }
        .company-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); border-color: var(--primary); }
        .company-card-logo {
            width: 56px; height: 56px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            background: var(--primary-soft);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary); font-size: 1.5rem;
            overflow: hidden;
        }
        .company-card-logo img { width: 100%; height: 100%; object-fit: cover; }
        .company-card-name { font-weight: 700; font-size: 1rem; color: var(--text); }
        .company-card-meta { font-size: 0.8rem; color: var(--text-3); display: flex; align-items: center; gap: 5px; }
        .company-card-actions { display: flex; gap: 6px; margin-top: auto; }

        /* ===== MODAL ===== */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden;
            transition: var(--transition);
        }
        .modal-backdrop.open { opacity: 1; visibility: visible; }
        .modal {
            background: var(--surface);
            border-radius: var(--radius-xl);
            padding: 28px;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-xl);
            transform: scale(0.9);
            transition: var(--transition);
        }
        .modal-backdrop.open .modal { transform: scale(1); }
        .modal-icon {
            width: 60px; height: 60px;
            background: var(--danger-soft);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--danger); font-size: 1.5rem;
            margin: 0 auto 18px;
        }
        .modal-title { font-size: 1.15rem; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .modal-text { font-size: 0.875rem; color: var(--text-2); text-align: center; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }

        /* ===== LOGO UPLOAD ===== */
        .logo-upload-area {
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            padding: 28px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        .logo-upload-area:hover { border-color: var(--primary); background: var(--primary-soft); }
        .logo-upload-area.has-file { border-style: solid; border-color: var(--primary); }
        .logo-preview {
            width: 85px; height: 85px;
            border-radius: var(--radius);
            object-fit: cover;
            margin: 0 auto 10px;
            display: block;
        }
        .logo-upload-area input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }

        /* ===== PAGINATION ===== */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            font-size: 0.85rem;
            color: var(--text-2);
            flex-wrap: wrap;
            gap: 10px;
        }
        .pagination { display: flex; gap: 4px; }
        .pagination a, .pagination span {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            color: var(--text-2);
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--surface);
            transition: var(--transition);
        }
        .pagination a:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); }
        .pagination .active span, .pagination [aria-current=page] span {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* ===== TOAST ===== */
        .toast-container {
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 18px;
            min-width: 280px;
            max-width: 400px;
            box-shadow: var(--shadow-lg);
            animation: toastIn 0.3s ease;
        }
        .toast.success { border-right: 4px solid var(--accent); }
        .toast.error   { border-right: 4px solid var(--danger); }
        .toast.warning { border-right: 4px solid var(--warning); }
        .toast.info    { border-right: 4px solid var(--info); }
        .toast-icon { font-size: 1.1rem; }
        .toast.success .toast-icon { color: var(--accent); }
        .toast.error   .toast-icon { color: var(--danger); }
        .toast.warning .toast-icon { color: var(--warning); }
        .toast.info    .toast-icon { color: var(--info); }
        .toast-text { font-size: 0.875rem; flex: 1; }
        .toast-close { background: none; border: none; color: var(--text-3); cursor: pointer; font-size: 1rem; transition: var(--transition); }
        .toast-close:hover { color: var(--text); }
        @keyframes toastIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-3);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 16px; opacity: 0.4; }
        .empty-state h3 { font-size: 1.1rem; color: var(--text-2); margin-bottom: 8px; }
        .empty-state p { font-size: 0.875rem; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .topbar-search input { width: 180px; }
            .topbar-search input:focus { width: 220px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                right: calc(-1 * var(--sidebar-width));
                box-shadow: none;
            }
            .sidebar.mobile-open {
                right: 0;
                box-shadow: -4px 0 24px rgba(0,0,0,0.3);
            }
            .topbar { right: 0; }
            .main-wrapper { margin-right: 0; padding: 16px; }
            .sidebar.collapsed ~ .main-wrapper { margin-right: 0; }
            .sidebar.collapsed ~ .topbar { right: 0; }
            .topbar-search { display: none; }
            .topbar-user-name, .topbar-user-role { display: none; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .filter-bar { gap: 8px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-value { font-size: 1.6rem; }
            .filter-bar .search-wrap .form-control { min-width: 0; width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <span class="sidebar-brand-icon"><i class="fas fa-store"></i></span>
        <span class="sidebar-brand-text">
            {{ $settings->store_name_ar ?? 'المتجر' }}
            <small>لوحة التحكم</small>
        </span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section-label">الرئيسية</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-gauge-high"></i></span>
            <span class="nav-label">لوحة التحكم</span>
        </a>

        <div class="nav-section-label">المنتجات</div>

        <a href="{{ route('admin.products.index') }}"
           class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-box"></i></span>
            <span class="nav-label">المنتجات</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tags"></i></span>
            <span class="nav-label">التصنيفات</span>
        </a>

        <a href="{{ route('admin.brands.index') }}"
           class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-certificate"></i></span>
            <span class="nav-label">الماركات</span>
        </a>

        <a href="{{ route('admin.attributes.index') }}"
           class="nav-item {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-list-check"></i></span>
            <span class="nav-label">الخصائص</span>
        </a>

        <div class="nav-section-label">المبيعات</div>

        <a href="{{ route('admin.orders.index') }}"
           class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
            <span class="nav-label">الطلبات</span>
        </a>

        <a href="{{ route('admin.customers.index') }}"
           class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            <span class="nav-label">العملاء</span>
        </a>

        <a href="{{ route('admin.coupons.index') }}"
           class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-ticket"></i></span>
            <span class="nav-label">الكوبونات</span>
        </a>

        <div class="nav-section-label">المحتوى</div>

        <a href="{{ route('admin.banners.index') }}"
           class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-images"></i></span>
            <span class="nav-label">البنرات</span>
        </a>

        <a href="{{ route('admin.pages.index') }}"
           class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-file-lines"></i></span>
            <span class="nav-label">الصفحات</span>
        </a>

        <a href="{{ route('admin.faqs.index') }}"
           class="nav-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-circle-question"></i></span>
            <span class="nav-label">الأسئلة الشائعة</span>
        </a>

        <a href="{{ route('admin.newsletter') }}"
           class="nav-item {{ request()->routeIs('admin.newsletter') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-envelope-open-text"></i></span>
            <span class="nav-label">النشرة البريدية</span>
        </a>

        <a href="{{ route('admin.messages') }}"
           class="nav-item {{ request()->routeIs('admin.messages') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-comments"></i></span>
            <span class="nav-label">الرسائل</span>
        </a>

        <div class="nav-section-label">التقارير</div>

        <a href="{{ route('admin.reports.sales') }}"
           class="nav-item {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span class="nav-label">تقرير المبيعات</span>
        </a>

        <a href="{{ route('admin.reports.products') }}"
           class="nav-item {{ request()->routeIs('admin.reports.products') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
            <span class="nav-label">تقرير المنتجات</span>
        </a>

        <a href="{{ route('admin.reports.customers') }}"
           class="nav-item {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
            <span class="nav-label">تقرير العملاء</span>
        </a>

        <a href="{{ route('admin.reports.inventory') }}"
           class="nav-item {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-warehouse"></i></span>
            <span class="nav-label">تقرير المخزون</span>
        </a>

        <div class="nav-section-label">النظام</div>

        <a href="{{ route('admin.settings') }}"
           class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-gear"></i></span>
            <span class="nav-label">الإعدادات</span>
        </a>

        <a href="{{ route('admin.activity-logs') }}"
           class="nav-item {{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-clock-rotate-left"></i></span>
            <span class="nav-label">سجل النشاط</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
            @csrf
        </form>
        <div class="sidebar-user" onclick="document.getElementById('logoutForm').submit()" style="cursor:pointer" title="تسجيل الخروج">
            <div class="sidebar-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">مدير النظام</div>
            </div>
        </div>
    </div>
</aside>

<!-- ===== TOPBAR ===== -->
<header class="topbar" id="topbar">
    <button class="topbar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="تبديل القائمة الجانبية">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="topbar-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-house"></i></a>
        <span class="sep"><i class="fas fa-chevron-left"></i></span>
        @yield('breadcrumb', '<span class="current">لوحة التحكم</span>')
    </nav>

    <div class="topbar-search">
        <input type="text" id="globalSearch" placeholder="بحث سريع..." autocomplete="off">
        <i class="fas fa-magnifying-glass search-icon"></i>
    </div>

    <div class="topbar-actions">
        <!-- Dark mode toggle -->
        <button class="topbar-btn" id="themeToggle" onclick="toggleTheme()" title="تبديل المظهر" aria-label="تبديل المظهر">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <div class="topbar-divider"></div>

        <!-- User menu -->
        <div class="topbar-user" id="topbarUser">
            <div class="topbar-user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div class="topbar-user-name">{{ auth()->user()->name }}</div>
                <div class="topbar-user-role">سوبر أدمن</div>
            </div>
            <i class="fas fa-chevron-down" style="font-size:0.7rem; color: var(--text-3); margin-right: 4px;"></i>

            <div class="topbar-user-dropdown">
                <a href="{{ route('store.home') }}" class="dropdown-item" target="_blank">
                    <i class="fas fa-store"></i> عرض المتجر
                </a>
                <a href="{{ route('admin.settings') }}" class="dropdown-item">
                    <i class="fas fa-gear"></i> الإعدادات
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item danger" onclick="event.preventDefault(); document.getElementById('logoutForm').submit()">
                    <i class="fas fa-right-from-bracket"></i> تسجيل الخروج
                </a>
            </div>
        </div>
    </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-wrapper" id="mainWrapper">

    {{-- Global flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" id="flashAlert">
            <i class="fas fa-circle-check"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="this.closest('.alert').remove()"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" id="flashAlert">
            <i class="fas fa-circle-xmark"></i>
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="this.closest('.alert').remove()"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info" id="flashAlert">
            <i class="fas fa-circle-info"></i>
            <span>{{ session('info') }}</span>
            <button class="alert-close" onclick="this.closest('.alert').remove()"><i class="fas fa-xmark"></i></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- ===== DELETE CONFIRMATION MODAL ===== -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal">
        <div class="modal-icon"><i class="fas fa-trash-can"></i></div>
        <h3 class="modal-title">تأكيد الحذف</h3>
        <p class="modal-text" id="deleteModalText">هل أنت متأكد من حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeDeleteModal()"><i class="fas fa-xmark"></i> إلغاء</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-can"></i> نعم، احذف</button>
            </form>
        </div>
    </div>
</div>

<!-- ===== TOAST CONTAINER ===== -->
<div class="toast-container" id="toastContainer"></div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    /* ===== SIDEBAR TOGGLE ===== */
    const sidebar    = document.getElementById('sidebar');
    const topbar     = document.getElementById('topbar');
    const mainWrap   = document.getElementById('mainWrapper');
    const overlay    = document.getElementById('sidebarOverlay');
    let isMobile     = window.innerWidth <= 768;

    function toggleSidebar() {
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }

    // Restore sidebar state on desktop
    window.addEventListener('resize', () => {
        isMobile = window.innerWidth <= 768;
        if (!isMobile) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }
    });

    if (!isMobile && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    /* ===== THEME TOGGLE ===== */
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    function toggleTheme() {
        const current = html.getAttribute('data-theme');
        const next    = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    }

    applyTheme(localStorage.getItem('theme') || 'light');

    /* ===== DELETE MODAL ===== */
    function confirmDelete(url, label) {
        const modal = document.getElementById('deleteModal');
        const text  = document.getElementById('deleteModalText');
        const form  = document.getElementById('deleteForm');
        text.textContent = `هل أنت متأكد من حذف "${label}"؟ لا يمكن التراجع عن هذا الإجراء.`;
        form.action = url;
        modal.classList.add('open');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    /* ===== TOAST ===== */
    function showToast(message, type = 'success') {
        const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fas ${icons[type]} toast-icon"></i>
            <span class="toast-text">${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>
        `;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => toast.style.opacity = '0', 4000);
        setTimeout(() => toast.remove(), 4400);
    }

    /* ===== AUTO-DISMISS FLASH ===== */
    const flashAlert = document.getElementById('flashAlert');
    if (flashAlert) setTimeout(() => flashAlert.style.opacity = '0', 4000);

    /* ===== CHART.JS GLOBAL DEFAULTS ===== */
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.size   = 12;
</script>

@stack('scripts')
</body>
</html>



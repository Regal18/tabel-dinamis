<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KINERA — Kinerja Informasi & Data Daerah Bidang IPW') — BAPPEDA Maluku Utara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        /* ===== Reset & CSS Variables ===== */
        :root {
            --deep-blue-900: #0a1628;
            --deep-blue-800: #0f2240;
            --deep-blue-700: #143464;
            --deep-blue-600: #1a4480;
            --deep-blue-500: #1e5299;
            --deep-blue-400: #2b6cb0;
            --deep-blue-300: #4a90d9;
            --deep-blue-200: #90bef4;
            --deep-blue-100: #c5dcf7;
            --deep-blue-50:  #ebf2fc;

            --white: #ffffff;
            --gray-50:  #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;

            --success: #059669;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --warning: #d97706;
            --warning-bg: #fffbeb;
            --warning-border: #fde68a;
            --info-bg: var(--deep-blue-50);
            --info-border: var(--deep-blue-200);
            --info-text: var(--deep-blue-700);

            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 2px rgba(10,22,40,0.05);
            --shadow-md: 0 4px 12px rgba(10,22,40,0.08);
            --shadow-lg: 0 8px 24px rgba(10,22,40,0.12);
            --transition: 0.2s ease;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background-color: #f6f8fc;
            background-image: 
                linear-gradient(to right, rgba(29, 78, 216, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(29, 78, 216, 0.035) 1px, transparent 1px),
                radial-gradient(rgba(29, 78, 216, 0.08) 1.25px, transparent 1.25px);
            background-size: 40px 40px, 40px 40px, 20px 20px;
            background-position: 0 0, 0 0, 10px 10px;
            font-size: 14px;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            position: relative;
        }

        /* ===== Decorative Background Ambient Elements ===== */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: -1;
            background:
                radial-gradient(circle 650px at 8% 18%, rgba(29, 78, 216, 0.055), transparent 70%),
                radial-gradient(circle 550px at 92% 22%, rgba(56, 189, 248, 0.065), transparent 70%),
                radial-gradient(circle 750px at 50% 95%, rgba(30, 58, 138, 0.04), transparent 75%);
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 320px;
            pointer-events: none;
            z-index: -1;
            background: radial-gradient(ellipse 70% 60% at 50% 0%, rgba(29, 78, 216, 0.07), transparent 70%);
        }

        /* ===== Header (Clean, Elegant & Modern) ===== */
        .td-header {
            background: rgba(10, 22, 40, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--white);
            padding: 0 1.25rem;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(5, 12, 24, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .td-header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }
        .td-header-logo {
            width: 36px;
            height: 36px;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
            overflow: hidden;
            padding: 3px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .td-header-brand:hover .td-header-logo {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }
        .td-header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .td-brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .td-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #ffffff;
            margin: 0;
            line-height: 1.15;
            display: inline-block;
        }
        .td-brand-sub {
            font-size: 0.62rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            color: #94a3b8;
            line-height: 1.1;
            margin-top: 1px;
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .td-brand-sub {
                font-size: 0.52rem;
                letter-spacing: 0.02em;
            }
        }
        .td-header-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .td-header-link {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 0.38rem 0.75rem;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .td-header-link svg {
            width: 14px;
            height: 14px;
            color: rgba(255, 255, 255, 0.75);
            transition: color 0.15s ease;
        }
        .td-header-link:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }
        .td-header-link.active {
            background: rgba(37, 99, 235, 0.32);
            border-color: rgba(96, 165, 250, 0.45);
            color: #ffffff;
            font-weight: 600;
        }
        .td-header-link.active svg {
            color: #93c5fd;
        }
        .td-header-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(29, 78, 216, 0.25);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #93c5fd;
            padding: 0.35rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition: all 0.15s ease;
        }
        .td-header-badge:hover {
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
        }

        /* ===== Container ===== */
        .td-container {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 1.25rem 1rem;
        }

        /* ===== Card ===== */
        .td-card {
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .td-card h2 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--deep-blue-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--deep-blue-100);
        }
        .td-card h2 .td-h2-icon {
            width: 28px;
            height: 28px;
            background: var(--deep-blue-50);
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        /* ===== Form Elements ===== */
        .td-form-group { margin-bottom: 0.95rem; }
        .td-form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: #334155;
            margin-bottom: 0.35rem;
            letter-spacing: -0.01em;
        }
        .td-form-group select,
        .td-form-group input[type="text"],
        .td-form-group input[type="file"] {
            width: 100%;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: inherit;
            color: #0f172a;
            background-color: var(--white);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .td-form-group select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 14px 14px;
            padding-right: 2.25rem;
            cursor: pointer;
        }
        .td-form-group select:disabled {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
        }
        .td-form-group select:focus,
        .td-form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }
        .td-form-group select:disabled,
        .td-form-group input:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ===== Buttons ===== */
        .td-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.55rem 1.05rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: transform 0.16s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, color 0.15s ease;
            text-decoration: none;
            user-select: none;
        }
        .td-btn-primary {
            background: #1d4ed8;
            color: var(--white);
            border: 1px solid #1e40af;
            box-shadow: 0 1px 3px rgba(29, 78, 216, 0.25), 0 1px 2px -1px rgba(29, 78, 216, 0.2);
        }
        .td-btn-primary:hover:not(:disabled) {
            background: #1e40af;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
            transform: translateY(-1.5px);
        }
        .td-btn-primary:active:not(:disabled) {
            transform: scale(0.975);
            box-shadow: 0 1px 3px rgba(29, 78, 216, 0.2);
        }
        .td-btn-secondary {
            background: #334155;
            color: var(--white);
            border: 1px solid #1e293b;
        }
        .td-btn-secondary:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }
        .td-btn-secondary:active {
            transform: scale(0.975);
        }
        .td-btn-outline {
            background: var(--white);
            border: 1px solid #cbd5e1;
            color: #334155;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            font-weight: 500;
        }
        .td-btn-outline:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
        }
        .td-btn-outline:active:not(:disabled) {
            transform: scale(0.975);
        }
        .td-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* ===== Table ===== */
        .td-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .td-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.825rem;
        }
        .td-table th, .td-table td {
            padding: 0.55rem 0.75rem;
            border: 1px solid var(--gray-200);
            text-align: left;
        }
        .td-table th {
            background: var(--deep-blue-800);
            color: var(--white);
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.8rem;
        }
        .td-table tr:nth-child(even) { background: var(--gray-50); }
        .td-table tr:hover { background: var(--deep-blue-50); }

        /* ===== Alerts ===== */
        .td-alert {
            padding: 0.7rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            border-width: 1px;
            border-style: solid;
        }
        .td-alert-success { background: var(--success-bg); color: var(--success); border-color: var(--success-border); }
        .td-alert-error   { background: var(--danger-bg);  color: var(--danger);  border-color: var(--danger-border);  }
        .td-alert-info    { background: var(--info-bg);    color: var(--info-text); border-color: var(--info-border); }
        .td-alert-warning { background: var(--warning-bg); color: var(--warning); border-color: var(--warning-border); }

        /* ===== Footer ===== */
        .td-footer {
            text-align: center;
            padding: 1.5rem 1rem;
            color: var(--gray-500);
            font-size: 0.75rem;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            margin-top: 2rem;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
        }

        /* ===== Utilities ===== */
        .td-hidden { display: none !important; }
        .td-mt-1 { margin-top: 0.5rem; }
        .td-mt-2 { margin-top: 1rem; }

        /* ===== Spinner ===== */
        .td-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--gray-300);
            border-top-color: var(--deep-blue-500);
            border-radius: 50%;
            animation: td-spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 0.4rem;
        }
        @keyframes td-spin { to { transform: rotate(360deg); } }

        /* ===== Responsive ===== */
        @media (min-width: 768px) {
            body { font-size: 15px; }
            .td-header { padding: 0 2rem; }
            .td-header h1 { font-size: 1.1rem; }
            .td-container { padding: 1.75rem 2rem; }
            .td-card { padding: 1.5rem; margin-bottom: 1.5rem; }
            .td-table { font-size: 0.85rem; }
            .td-table th, .td-table td { padding: 0.6rem 0.85rem; }
        }
        /* ===== Subtle Micro-Animations ===== */
        @keyframes tdFadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes tdPulseLive {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(29, 78, 216, 0.45);
            }
            50% {
                transform: scale(1.18);
                box-shadow: 0 0 0 4px rgba(29, 78, 216, 0);
            }
        }
        @keyframes tdItemPopIn {
            0% {
                opacity: 0;
                transform: translateY(-8px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes tdViewFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes tdBadgePop {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* ===== Accessibility: Prefers Reduced Motion ===== */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* ===== Print Media Query ===== */
        @media print {
            body {
                background: #ffffff !important;
            }
            body::before, body::after {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="td-header">
        <a href="{{ route('tabel-dinamis.beranda') }}" class="td-header-brand" title="Menuju Beranda KINERA">
            <div class="td-header-logo">
                <img src="{{ asset('images/kinera-logo.png') }}" alt="Logo KINERA">
            </div>
            <div class="td-brand-text">
                <h1>KINERA</h1>
                <span class="td-brand-sub">Kinerja Informasi & Data Daerah Bidang IPW</span>
            </div>
        </a>
        <div class="td-header-actions">
            <a href="{{ route('tabel-dinamis.beranda') }}" class="td-header-link {{ request()->routeIs('tabel-dinamis.beranda') ? 'active' : '' }}" title="Halaman Utama & Profil KINERA">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('tabel-dinamis.index') }}" class="td-header-link {{ request()->routeIs('tabel-dinamis.index') ? 'active' : '' }}" title="Eksplorasi & Filter Tabel Dinamis">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                <span>Tabel Dinamis</span>
            </a>
            <a href="{{ route('tabel-dinamis.admin.indikator.index') }}" class="td-header-link {{ request()->routeIs('tabel-dinamis.admin.indikator.*') ? 'active' : '' }}" title="Kelola Master Data Indikator">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                <span>Master Indikator</span>
            </a>
            <a href="{{ route('tabel-dinamis.admin.impor.form') }}" class="td-header-link {{ request()->routeIs('tabel-dinamis.admin.impor.*') ? 'active' : '' }}" title="Unggah Berkas Excel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Admin Impor</span>
            </a>
            <span class="td-header-badge">
                <span>BAPPEDA Maluku Utara</span>
            </span>
        </div>
    </header>

    <main class="td-container">
        @yield('content')
    </main>

    <footer class="td-footer">
        &copy; {{ date('Y') }} BAPPEDA Provinsi Maluku Utara — KINERA (Kinerja Informasi & Data Daerah Bidang IPW)
    </footer>

    @stack('scripts')
</body>
</html>

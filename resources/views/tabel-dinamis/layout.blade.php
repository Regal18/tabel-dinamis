<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tabel Dinamis Indikator Bidang IPW') — BAPPEDA Maluku Utara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background: #f4f6fb;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Header ===== */
        .td-header {
            background: linear-gradient(135deg, var(--deep-blue-900) 0%, var(--deep-blue-800) 50%, var(--deep-blue-700) 100%);
            color: var(--white);
            padding: 0 1.25rem;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(10,22,40,0.25);
        }
        .td-header-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .td-header-logo {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .td-header h1 {
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        .td-header-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .td-header-link {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .td-header-link:hover {
            background: rgba(255,255,255,0.12);
            color: var(--white);
            border-color: rgba(255,255,255,0.35);
        }
        .td-header .td-badge {
            background: var(--deep-blue-400);
            color: var(--white);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
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
        .td-form-group { margin-bottom: 0.85rem; }
        .td-form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--gray-700);
            margin-bottom: 0.35rem;
            letter-spacing: 0.01em;
        }
        .td-form-group select,
        .td-form-group input[type="text"],
        .td-form-group input[type="file"] {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--gray-800);
            background: var(--white);
            transition: all var(--transition);
        }
        .td-form-group select:focus,
        .td-form-group input:focus {
            outline: none;
            border-color: var(--deep-blue-500);
            box-shadow: 0 0 0 3px rgba(30,82,153,0.12);
        }
        .td-form-group select:disabled,
        .td-form-group input:disabled {
            background: var(--gray-100);
            color: var(--gray-400);
            cursor: not-allowed;
        }

        /* ===== Buttons ===== */
        .td-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 1.1rem;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }
        .td-btn-primary {
            background: var(--deep-blue-600);
            color: var(--white);
            box-shadow: 0 1px 3px rgba(26,68,128,0.3);
        }
        .td-btn-primary:hover {
            background: var(--deep-blue-700);
            box-shadow: 0 2px 6px rgba(26,68,128,0.4);
            transform: translateY(-1px);
        }
        .td-btn-secondary {
            background: var(--deep-blue-800);
            color: var(--white);
        }
        .td-btn-secondary:hover {
            background: var(--deep-blue-900);
        }
        .td-btn-outline {
            background: transparent;
            border: 1.5px solid var(--deep-blue-500);
            color: var(--deep-blue-600);
        }
        .td-btn-outline:hover {
            background: var(--deep-blue-50);
            border-color: var(--deep-blue-600);
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
            color: var(--gray-400);
            font-size: 0.75rem;
            border-top: 1px solid var(--gray-200);
            margin-top: 1rem;
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
    </style>
    @stack('styles')
</head>
<body>
    <header class="td-header">
        <div class="td-header-brand">
            <div class="td-header-logo">📊</div>
            <h1>Tabel Dinamis Indikator Bidang IPW</h1>
        </div>
        <div class="td-header-actions">
            <a href="{{ route('tabel-dinamis.admin.impor.form') }}" class="td-header-link">
                <span>⚙️</span> Admin Impor
            </a>
            <span class="td-badge">BAPPEDA Maluku Utara</span>
        </div>
    </header>

    <main class="td-container">
        @yield('content')
    </main>

    <footer class="td-footer">
        &copy; {{ date('Y') }} BAPPEDA Provinsi Maluku Utara — Modul Tabel Dinamis Indikator Bidang IPW
    </footer>

    @stack('scripts')
</body>
</html>

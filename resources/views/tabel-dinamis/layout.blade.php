<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tabel Dinamis Indikator Bidang IPW') — BAPPEDA Maluku Utara</title>
    <style>
        /* === Reset & Base (mobile-first) === */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #1a1a2e;
            background: #f0f2f5;
            font-size: 14px;
        }

        /* === Header === */
        .td-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .td-header h1 { font-size: 1rem; font-weight: 600; }
        .td-header .td-badge {
            background: #e94560;
            color: #fff;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* === Container === */
        .td-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* === Card === */
        .td-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .td-card h2 {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            color: #16213e;
            border-bottom: 2px solid #e94560;
            padding-bottom: 0.4rem;
        }

        /* === Form elements === */
        .td-form-group { margin-bottom: 0.75rem; }
        .td-form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 0.85rem;
        }
        .td-form-group select,
        .td-form-group input[type="text"],
        .td-form-group input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .td-form-group select:focus,
        .td-form-group input:focus {
            outline: none;
            border-color: #e94560;
            box-shadow: 0 0 0 3px rgba(233,69,96,0.1);
        }

        /* === Button === */
        .td-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .td-btn-primary { background: #e94560; color: #fff; }
        .td-btn-primary:hover { background: #c73a54; }
        .td-btn-secondary { background: #16213e; color: #fff; }
        .td-btn-secondary:hover { background: #1a1a2e; }
        .td-btn-outline {
            background: transparent;
            border: 1px solid #16213e;
            color: #16213e;
        }
        .td-btn-outline:hover { background: #16213e; color: #fff; }
        .td-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* === Tabel === */
        .td-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .td-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        .td-table th, .td-table td {
            padding: 0.4rem 0.6rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        .td-table th {
            background: #16213e;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
        }
        .td-table tr:nth-child(even) { background: #f8f9fa; }
        .td-table tr:hover { background: #eef; }

        /* === Alert === */
        .td-alert {
            padding: 0.6rem 0.75rem;
            border-radius: 4px;
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
        }
        .td-alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .td-alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .td-alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .td-alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

        /* === Footer === */
        .td-footer {
            text-align: center;
            padding: 1rem;
            color: #666;
            font-size: 0.75rem;
        }

        /* === Utilitas === */
        .td-hidden { display: none !important; }
        .td-mt-1 { margin-top: 0.5rem; }
        .td-mt-2 { margin-top: 1rem; }

        /* === Spinner === */
        .td-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #ddd;
            border-top-color: #e94560;
            border-radius: 50%;
            animation: td-spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        @keyframes td-spin { to { transform: rotate(360deg); } }

        /* === Responsive: tablet ke atas === */
        @media (min-width: 768px) {
            body { font-size: 15px; }
            .td-header { padding: 1rem 2rem; }
            .td-header h1 { font-size: 1.25rem; }
            .td-container { padding: 1.5rem; }
            .td-card { padding: 1.5rem; margin-bottom: 1.5rem; }
            .td-table { font-size: 0.85rem; }
            .td-table th, .td-table td { padding: 0.5rem 0.75rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="td-header">
        <h1>📊 Tabel Dinamis Indikator Bidang IPW</h1>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <a href="{{ route('tabel-dinamis.admin.impor.form') }}" style="color: #fff; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.15); padding: 0.25rem 0.6rem; border-radius: 4px;">
                ⚙️ Admin Impor
            </a>
            <span class="td-badge">BAPPEDA Maluku Utara</span>
        </div>
    </header>

    <main class="td-container">
        @yield('content')
    </main>

    <footer class="td-footer">
        &copy; {{ date('Y') }} BAPPEDA Provinsi Maluku Utara — Modul Tabel Dinamis
    </footer>

    @stack('scripts')
</body>
</html>


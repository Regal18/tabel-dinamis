@extends(config('tabel_dinamis.layout'))

@section('title', 'KINERA — Kinerja Informasi & Data Daerah Bidang IPW')

@push('styles')
<style>
    /* ===== Toast Notification Container ===== */
    #td-alert-box {
        position: fixed;
        top: 76px;
        right: 20px;
        z-index: 9999;
        max-width: 420px;
        width: calc(100% - 40px);
        pointer-events: none;
    }
    #td-alert-box .td-alert {
        pointer-events: auto;
        box-shadow: 0 10px 30px -4px rgba(10, 22, 40, 0.22), 0 4px 10px rgba(10, 22, 40, 0.08);
        border-radius: var(--radius-md);
        padding: 0.85rem 1.15rem;
        margin-bottom: 0.6rem;
        animation: tdToastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
    }
    @keyframes tdToastSlideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* ===== Info Banner ===== */
    .td-banner-info {
        background: var(--deep-blue-50);
        border: 1px solid var(--deep-blue-200);
        border-left: 4px solid var(--deep-blue-500);
        color: var(--deep-blue-700);
        padding: 0.7rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .td-banner-info svg {
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        color: var(--deep-blue-500);
    }

    /* ===== Page Title ===== */
    .td-page-title {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--deep-blue-800);
        margin-bottom: 0.25rem;
    }
    .td-page-subtitle {
        font-size: 0.825rem;
        color: var(--gray-500);
        margin-bottom: 1.25rem;
    }

    /* ===== Two-Column Layout ===== */
    .td-layout-grid {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    @media (min-width: 992px) {
        .td-layout-grid {
            display: grid;
            grid-template-columns: 5fr 4fr;
            align-items: start;
            gap: 1.5rem;
        }
    }

    /* ===== Section Box (Efek Timbul / 3D Elevated Card) ===== */
    .td-section-box {
        background: var(--white);
        border: 1px solid rgba(20, 52, 100, 0.1);
        border-radius: var(--radius-lg);
        padding: 1.4rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 3px 6px -1px rgba(10, 25, 60, 0.04),
                    0 10px 28px -4px rgba(10, 35, 90, 0.09),
                    inset 0 1px 0 rgba(255, 255, 255, 1);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    border-color 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .td-section-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #1d4ed8;
    }
    .td-section-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px -4px rgba(10, 35, 90, 0.16),
                    0 8px 16px -2px rgba(10, 25, 60, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 1);
        border-color: rgba(29, 78, 216, 0.28);
    }

    /* ===== Section Header & Typography ===== */
    .td-section-header {
        margin-bottom: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.85);
    }
    .td-section-title-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        flex: 1;
        min-width: 0;
    }
    .td-section-title {
        font-size: 0.975rem;
        font-weight: 800;
        letter-spacing: -0.015em;
        line-height: 1.25;
        color: #0f2d59;
        margin: 0;
        transition: color 0.2s ease;
    }
    .td-section-desc {
        font-size: 0.72rem;
        font-weight: 500;
        color: #64748b;
        letter-spacing: 0.01em;
        line-height: 1.2;
    }
    .td-section-box:hover .td-section-title {
        color: #1d4ed8;
    }

    /* ===== Step Badge (Clean & Modern Minimalist) ===== */
    .td-step-badge {
        background: #1d4ed8;
        color: #ffffff;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.01em;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(29, 78, 216, 0.2);
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .td-section-box:hover .td-step-badge {
        background: #1e40af;
        transform: scale(1.06);
    }

    /* ===== Search Input ===== */
    .td-search-box {
        position: relative;
        margin-bottom: 0.5rem;
    }
    .td-search-input {
        width: 100%;
        padding: 0.55rem 0.75rem 0.55rem 2.25rem;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-family: inherit;
        color: var(--gray-800);
        background: var(--white);
        transition: all var(--transition);
    }
    .td-search-input:focus {
        outline: none;
        border-color: var(--deep-blue-500);
        box-shadow: 0 0 0 3px rgba(30,82,153,0.12);
    }
    .td-search-input:disabled {
        background: var(--gray-100);
        cursor: not-allowed;
    }
    .td-search-icon {
        position: absolute;
        left: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        pointer-events: none;
    }
    .td-search-icon svg {
        width: 16px;
        height: 16px;
    }

    /* ===== Checkbox Grid ===== */
    .td-checkbox-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
    }
    .td-checkbox-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--white);
        border: 1.5px solid var(--gray-200);
        padding: 0.45rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        color: var(--gray-700);
        cursor: pointer;
        user-select: none;
        box-shadow: 0 1px 3px rgba(10, 22, 40, 0.04);
        transition: all 0.2s ease;
    }
    .td-checkbox-pill:hover {
        border-color: var(--deep-blue-300);
        background: var(--deep-blue-50);
        box-shadow: 0 3px 8px rgba(10, 22, 40, 0.07);
        transform: translateY(-1px);
    }
    .td-checkbox-pill:has(input:checked) {
        border-color: #1d4ed8;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.12);
        transform: translateY(-1px);
    }
    .td-checkbox-pill input[type="checkbox"] {
        margin: 0;
        cursor: pointer;
        accent-color: #1d4ed8;
        width: 14px;
        height: 14px;
    }

    /* ===== Selected Data Container ===== */
    .td-selected-container {
        min-height: 90px;
        background: #f0f5fc;
        border: 2px dashed #bfdbfe;
        border-radius: var(--radius-md);
        padding: 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .td-selected-item {
        background: var(--white);
        border: 1px solid rgba(29, 78, 216, 0.15);
        border-left: 4.5px solid #1d4ed8;
        padding: 0.75rem 0.95rem;
        border-radius: var(--radius-sm);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.6rem;
        box-shadow: 0 3px 10px rgba(13, 40, 109, 0.08), inset 0 1px 0 rgba(255, 255, 255, 1);
        transition: all var(--transition);
    }
    .td-selected-item:hover {
        box-shadow: 0 6px 18px rgba(13, 40, 109, 0.14);
        transform: translateY(-2px);
    }
    .td-selected-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--deep-blue-800);
        line-height: 1.35;
    }
    .td-selected-meta {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.3rem;
    }
    .td-btn-delete {
        background: none;
        border: none;
        color: var(--gray-400);
        cursor: pointer;
        font-size: 0.85rem;
        line-height: 1;
        padding: 0.3rem;
        border-radius: 4px;
        transition: all var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .td-btn-delete:hover {
        color: var(--danger);
        background: var(--danger-bg);
    }

    /* ===== Action Buttons ===== */
    .td-action-buttons {
        display: flex;
        gap: 0.6rem;
        margin-top: 1rem;
    }
    @media (max-width: 767px) {
        .td-action-buttons {
            position: sticky;
            bottom: 0.75rem;
            z-index: 40;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            padding: 0.65rem;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 18px rgba(10, 22, 40, 0.18);
            border: 1px solid rgba(15, 34, 64, 0.12);
        }
    }

    /* ===== Result Table ===== */
    .td-table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-md);
        position: relative;
    }
    .td-table-dynamic {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.825rem;
        min-width: 600px;
    }
    .td-table-dynamic th,
    .td-table-dynamic td {
        border-right: 1px solid var(--gray-200);
        border-bottom: 1px solid var(--gray-200);
        padding: 0.65rem 0.85rem;
    }
    .td-table-dynamic th {
        background: var(--deep-blue-800);
        color: var(--white);
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
        font-size: 0.8rem;
        letter-spacing: 0.01em;
    }
    /* Sticky First Column for Wilayah */
    .td-table-dynamic th.td-th-wilayah {
        text-align: left;
        vertical-align: middle;
        min-width: 190px;
        position: sticky;
        left: 0;
        z-index: 5;
        background: var(--deep-blue-900);
        box-shadow: 3px 0 8px -2px rgba(10, 22, 40, 0.35);
    }
    .td-table-dynamic tbody td.td-td-wilayah {
        position: sticky;
        left: 0;
        z-index: 3;
        font-weight: 600;
        background-color: var(--white);
        box-shadow: 3px 0 8px -2px rgba(10, 22, 40, 0.1);
    }
    .td-table-dynamic tbody tr:nth-child(even) td.td-td-wilayah {
        background-color: var(--gray-50);
    }
    .td-table-dynamic tbody tr:hover td.td-td-wilayah {
        background-color: var(--deep-blue-50);
    }

    .td-table-dynamic td.td-align-right {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .td-table-dynamic td.td-align-center {
        text-align: center;
        color: var(--gray-400);
    }
    .td-table-dynamic tbody tr:nth-child(even) td:not(.td-td-wilayah) {
        background-color: var(--gray-50);
    }
    .td-table-dynamic tbody tr:hover td:not(.td-td-wilayah) {
        background-color: var(--deep-blue-50);
    }

    /* Tags for Realisasi and Target in Table Header */
    .td-table-tag {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin-right: 0.35rem;
        display: inline-block;
        vertical-align: middle;
    }
    .td-tag-realisasi {
        background: rgba(56, 189, 248, 0.22);
        color: #7dd3fc;
        border: 1px solid rgba(56, 189, 248, 0.45);
    }
    .td-tag-target {
        background: rgba(251, 191, 36, 0.22);
        color: #fde68a;
        border: 1px solid rgba(251, 191, 36, 0.45);
    }

    /* Metadata Pills under Indikator */
    .td-meta-pills-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.45rem;
    }
    .td-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .td-meta-opd {
        background: var(--deep-blue-50);
        color: var(--deep-blue-700);
        border: 1px solid var(--deep-blue-200);
    }
    .td-meta-satuan {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    /* Badge Available for Years */
    .td-badge-available {
        font-size: 0.65rem;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 0.05rem 0.35rem;
        border-radius: 10px;
        font-weight: 600;
        margin-left: 0.2rem;
    }

    /* ===== Result Header ===== */
    .td-result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .td-result-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--deep-blue-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .td-h2-icon {
        width: 30px;
        height: 30px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: var(--radius-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(29, 78, 216, 0.08);
    }
    .td-h2-icon svg {
        width: 16px;
        height: 16px;
    }
    .td-switch-btn svg,
    .td-export-group .td-btn svg {
        width: 14px;
        height: 14px;
        margin-right: 0.35rem;
        vertical-align: -2px;
        flex-shrink: 0;
    }
    .td-export-group {
        display: flex;
        gap: 0.5rem;
    }

    /* ===== Empty State ===== */
    .td-empty-state {
        text-align: center;
        color: var(--gray-400);
        font-size: 0.825rem;
        padding: 1.5rem 1rem;
    }
    .td-empty-state svg {
        width: 32px;
        height: 32px;
        margin-bottom: 0.5rem;
        opacity: 0.4;
    }

    /* ===== Counter Badge ===== */
    .td-counter-badge {
        margin-left: auto;
        font-size: 0.72rem;
        font-weight: 700;
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.22rem 0.6rem;
        border-radius: 9999px;
        box-shadow: 0 1px 3px rgba(29, 78, 216, 0.1);
        letter-spacing: 0.02em;
        transition: all 0.2s ease;
    }

    /* ===== Helper Text ===== */
    .td-helper-text {
        display: block;
        color: var(--gray-500);
        margin-top: 0.3rem;
        font-size: 0.78rem;
    }

    /* ===== Select All Pill ===== */
    .td-select-all-pill {
        font-weight: 600;
        margin-bottom: 0.6rem;
        display: inline-flex;
        background: var(--deep-blue-50);
        border-color: var(--deep-blue-200);
    }

    /* ===== Tambah Button Full Width ===== */
    .td-btn-tambah-full {
        width: 100%;
        padding: 0.65rem 1rem;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }

    /* ===== Result Table Card (Elevated 3D Depth) ===== */
    #td-hasil-wrapper {
        border: 1px solid rgba(15, 34, 64, 0.08);
        border-radius: var(--radius-lg);
        box-shadow: 0 4px 6px -1px rgba(10, 22, 40, 0.04),
                    0 10px 24px -4px rgba(10, 22, 40, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 1);
        position: relative;
        overflow: hidden;
    }
    #td-hasil-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #1d4ed8;
    }

    /* ===== Table Toolbar & Search ===== */
    .td-table-actions-toolbar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    .td-table-search-input {
        padding: 0.42rem 0.75rem 0.42rem 2rem;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        width: 190px;
        background-color: var(--white);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 0.6rem center;
        background-size: 13px 13px;
        transition: all 0.2s ease;
    }
    .td-table-search-input:focus {
        outline: none;
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        width: 230px;
    }

    /* ===== View Switcher (Tabel vs Grafik) ===== */
    .td-view-switcher {
        display: inline-flex;
        background: #f1f5f9;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        padding: 2px;
        gap: 2px;
    }
    .td-switch-btn {
        background: transparent;
        border: none;
        padding: 0.38rem 0.8rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-600);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .td-switch-btn:hover {
        color: var(--deep-blue-700);
    }
    .td-switch-btn.active {
        background: var(--white);
        color: var(--deep-blue-800);
        box-shadow: 0 1px 3px rgba(10, 22, 40, 0.12);
    }

    /* ===== Chart Panel ===== */
    .td-chart-panel {
        padding: 1.25rem;
        background: var(--white);
    }
    .td-chart-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--gray-200);
    }
    .td-chart-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        align-items: flex-end;
    }
    .td-chart-select {
        padding: 0.42rem 0.75rem;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        background: var(--white);
        color: var(--gray-800);
        font-weight: 500;
        cursor: pointer;
    }
    .td-chart-select:focus {
        outline: none;
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }
    .td-chart-canvas-container {
        position: relative;
        height: 420px;
        width: 100%;
    }

    /* ===== Sortable Header ===== */
    .td-sortable-th {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s ease;
    }
    .td-sortable-th:hover {
        background-color: #163e7d !important;
    }
    .td-sort-icon {
        display: inline-block;
        margin-left: 0.35rem;
        font-size: 0.75rem;
        opacity: 0.6;
        vertical-align: middle;
    }
    .td-sort-active {
        background-color: #163e7d !important;
    }
    .td-sort-active .td-sort-icon {
        opacity: 1;
        color: #38bdf8;
    }

    /* ===== TFoot Statistical Summary ===== */
    .td-table-dynamic tfoot tr {
        font-weight: 600;
        border-top: 2px solid var(--deep-blue-300);
    }
    .td-table-dynamic tfoot td {
        padding: 0.55rem 0.75rem;
        border: 1px solid var(--gray-200);
        font-size: 0.82rem;
    }
    .td-tfoot-avg {
        background: #f0f7ff !important;
        color: #1e40af;
    }
    .td-tfoot-max {
        background: #f0fdf4 !important;
        color: #166534;
    }
    .td-tfoot-min {
        background: #fffbeb !important;
        color: #92400e;
    }

    /* ===== Badge Empty (Year Filter) ===== */
    .td-badge-empty {
        font-size: 0.65rem;
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        padding: 0.05rem 0.35rem;
        border-radius: 10px;
        font-weight: 500;
        margin-left: 0.2rem;
    }

    /* ===== Official Print Styles ===== */
    .td-print-only {
        display: none;
    }
    @media print {
        @page {
            size: landscape;
            margin: 10mm 12mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 10.5pt !important;
        }
        .td-header, .td-layout-grid, .td-action-buttons, .td-banner-info,
        .td-result-header, .td-footer, #td-alert-box, .td-page-title, .td-page-subtitle,
        .td-chart-panel, .td-view-switcher {
            display: none !important;
        }
        .td-container {
            padding: 0 !important;
            max-width: 100% !important;
        }
        #td-hasil-wrapper {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
        }
        #td-hasil-wrapper::before {
            display: none !important;
        }
        .td-print-only {
            display: block !important;
            margin-bottom: 12pt;
        }
        .td-table-scroll {
            overflow: visible !important;
        }
        .td-table-dynamic {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .td-table-dynamic th, .td-table-dynamic td {
            border: 1px solid #333333 !important;
            padding: 4pt 6pt !important;
            color: #000000 !important;
            font-size: 9pt !important;
        }
        .td-table-dynamic th {
            background: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .td-th-wilayah, .td-td-wilayah {
            position: static !important;
            box-shadow: none !important;
            border-right: 1px solid #333333 !important;
        }
    }
</style>
@endpush

@section('content')
<div id="td-alert-box"></div>

<!-- Banner Kewajiban Maksimal Data (Jika Dibatasi) -->
@if(config('tabel_dinamis.maks_data_per_tabel'))
<div class="td-banner-info">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
    <span>Hanya dapat memilih maksimal {{ config('tabel_dinamis.maks_data_per_tabel') }} data per tabel</span>
</div>
@endif

<!-- Page Title -->
<h1 class="td-page-title">Filter Tabel Dinamis</h1>
<p class="td-page-subtitle">Pilih indikator, periode, dan wilayah untuk membentuk tabel data.</p>

<div class="td-layout-grid">
    <!-- ====== Kolom 1: Pemilihan Subjek, Indikator, Jenis & Tahun ====== -->
    <div>
        <div class="td-section-box">
            <div class="td-section-header">
                <span class="td-step-badge">1</span>
                <div class="td-section-title-wrap">
                    <h3 class="td-section-title">Pilih Indikator & Periode</h3>
                    <span class="td-section-desc">Pilih subjek, indikator, tipe data & rentang tahun</span>
                </div>
            </div>

            <!-- Dropdown Subjek -->
            <div class="td-form-group">
                <label for="td-subjek">Subjek / Sektor Bidang IPW</label>
                <select id="td-subjek">
                    <option value="">— Memuat Subjek... —</option>
                </select>
            </div>

            <!-- Pencarian & Dropdown Indikator (Debounce) -->
            <div class="td-form-group">
                <label for="td-cari-indikator">Pencarian & Daftar Indikator</label>
                <div class="td-search-box">
                    <span class="td-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                    <input type="text" id="td-cari-indikator" class="td-search-input" placeholder="Ketik kata kunci untuk mencari indikator..." disabled>
                </div>
                <select id="td-indikator" disabled>
                    <option value="">— Pilih Subjek Terlebih Dahulu —</option>
                </select>
                <small id="td-info-indikator" class="td-helper-text"></small>
            </div>

            <!-- Pilihan Jenis Data -->
            <div class="td-form-group">
                <label for="td-jenis">Jenis Data</label>
                <select id="td-jenis" disabled>
                    <option value="Realisasi">Realisasi</option>
                    <option value="Target">Target</option>
                </select>
            </div>

            <!-- Centang Tahun (2021-2026) -->
            <div class="td-form-group">
                <label>Tahun (Rentang 2021 – 2026)</label>
                <div id="td-tahun-list" class="td-checkbox-wrap">
                    <span style="color: var(--gray-400); font-size: 0.8rem;">Pilih indikator terlebih dahulu.</span>
                </div>
            </div>

            <!-- Tombol Tambah -->
            <button type="button" id="td-btn-tambah" class="td-btn td-btn-primary td-btn-tambah-full" disabled>
                Tambah ke Data Terpilih
            </button>
        </div>
    </div>

    <!-- ====== Kolom 2: Data Terpilih & Wilayah & Aksi ====== -->
    <div>
        <!-- Kontainer Data Terpilih (Maks 2) -->
        <div class="td-section-box">
            <div class="td-section-header">
                <span class="td-step-badge">2</span>
                <div class="td-section-title-wrap">
                    <h3 class="td-section-title">Data Terpilih</h3>
                    <span class="td-section-desc">Daftar seri indikator dalam tabel</span>
                </div>
                <span id="td-data-counter" class="td-counter-badge">(0 data)</span>
            </div>

            <div id="td-data-terpilih" class="td-selected-container">
                <div id="td-terpilih-kosong" class="td-empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    <div>Belum ada data terpilih.<br>Klik <strong>Tambah</strong> pada panel sebelah kiri.</div>
                </div>
            </div>
        </div>

        <!-- Centang Wilayah (Judul Baris) -->
        <div class="td-section-box">
            <div class="td-section-header">
                <span class="td-step-badge">3</span>
                <div class="td-section-title-wrap">
                    <h3 class="td-section-title">Judul Baris (Wilayah)</h3>
                    <span class="td-section-desc">Pilih kabupaten / kota Maluku Utara</span>
                </div>
            </div>

            <div class="td-form-group">
                <label class="td-checkbox-pill td-select-all-pill">
                    <input type="checkbox" id="td-semua-wilayah" checked>
                    <span>Pilih Semua Wilayah</span>
                </label>
                <div id="td-wilayah-list" class="td-checkbox-wrap">
                    <span style="color: var(--gray-400); font-size: 0.8rem;">Memuat daftar wilayah...</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi: Submit & Atur Ulang -->
        <div class="td-action-buttons">
            <button type="button" id="td-btn-submit" class="td-btn td-btn-primary" style="flex: 2;" disabled>
                Tampilkan Tabel
            </button>
            <button type="button" id="td-btn-reset" class="td-btn td-btn-outline" style="flex: 1;">
                Atur Ulang
            </button>
        </div>
    </div>
</div>

<!-- Kartu Tabel Hasil Dinamis Header Dua Tingkat -->
<div id="td-hasil-wrapper" class="td-card td-hidden" style="margin-top: 1.5rem;">
    <!-- Kop Cetak Resmi (Hanya Tampil Saat Dicetak / PDF) -->
    <div class="td-print-only">
        <div style="text-align: center; border-bottom: 2.5px double #000000; padding-bottom: 6px; margin-bottom: 10px;">
            <h2 style="font-size: 13pt; font-weight: 800; margin: 0; text-transform: uppercase;">PEMERINTAH PROVINSI MALUKU UTARA</h2>
            <h3 style="font-size: 12pt; font-weight: 700; margin: 2px 0 0 0;">BADAN PERENCANAAN PEMBANGUNAN DAERAH (BAPPEDA)</h3>
            <p style="font-size: 8.5pt; margin: 2px 0 0 0; color: #333;">Jl. Raya Lintas Halmahera, Gosale Puncak, Sofifi, Maluku Utara</p>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <div>
                <h4 style="font-size: 10.5pt; font-weight: 700; margin: 0; color: #111;">KINERA — KINERJA INFORMASI & DATA DAERAH BIDANG IPW</h4>
                <p style="font-size: 8.5pt; color: #444; margin: 1px 0 0 0;">Laporan Penyajian Data Tabel Dinamis</p>
            </div>
            <div style="text-align: right; font-size: 8pt; color: #444;">
                <div>Dicetak: <strong>{{ date('d/m/Y H:i') }} WIT</strong></div>
            </div>
        </div>
    </div>

    <div class="td-result-header">
        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
            <div class="td-result-title">
                <span class="td-h2-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <line x1="10" y1="9" x2="8" y2="9"></line>
                    </svg>
                </span>
                <span id="td-result-title-text">Hasil Analisis Data</span>
                <span id="td-table-row-count" class="td-counter-badge" style="margin-left: 0.5rem; font-size: 0.7rem;"></span>
            </div>
            <!-- Toggle Switcher: Tabel Data vs Grafik Dinamis -->
            <div class="td-view-switcher">
                <button type="button" class="td-switch-btn active" id="td-switch-tabel">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="3" y1="15" x2="21" y2="15"></line>
                        <line x1="9" y1="9" x2="9" y2="21"></line>
                        <line x1="15" y1="9" x2="15" y2="21"></line>
                    </svg>
                    <span>Tabel Data</span>
                </button>
                <button type="button" class="td-switch-btn" id="td-switch-grafik">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span>Visualisasi Grafik</span>
                </button>
            </div>
        </div>

        <div class="td-table-actions-toolbar" id="td-tabel-toolbar">
            <input type="text" id="td-cari-tabel" placeholder="Cari wilayah..." class="td-table-search-input">
            <div class="td-export-group">
                <button type="button" id="td-btn-cetak" class="td-btn td-btn-outline" style="font-size: 0.8rem;" title="Cetak atau Simpan sebagai PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak / PDF</span>
                </button>
                <button type="button" id="td-export-xlsx" class="td-btn td-btn-outline" style="font-size: 0.8rem;" title="Unduh berkas Excel (.xlsx)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="8" y1="13" x2="16" y2="17"></line>
                        <line x1="16" y1="13" x2="8" y2="17"></line>
                    </svg>
                    <span>Unduh Excel</span>
                </button>
                <button type="button" id="td-export-csv" class="td-btn td-btn-outline" style="font-size: 0.8rem;" title="Unduh berkas CSV (.csv)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    <span>Unduh CSV</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tampilan 1: Tabel Header Dua Tingkat (Bisa Digeser Horizontal) -->
    <div id="td-view-tabel" class="td-table-scroll">
        <table class="td-table-dynamic" id="td-dynamic-table">
            <thead id="td-table-head"></thead>
            <tbody id="td-table-body"></tbody>
            <tfoot id="td-table-foot"></tfoot>
        </table>
    </div>

    <!-- Tampilan 2: Visualisasi Grafik Interaktif -->
    <div id="td-view-grafik" class="td-chart-panel td-hidden">
        <div class="td-chart-toolbar">
            <div class="td-chart-controls">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray-600); display: block; margin-bottom: 3px;">Indikator:</label>
                    <select id="td-chart-indikator-select" class="td-chart-select"></select>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray-600); display: block; margin-bottom: 3px;">Bentuk Grafik:</label>
                    <select id="td-chart-tipe-select" class="td-chart-select">
                        <option value="bar">Grafik Batang (Perbandingan Antar Wilayah)</option>
                        <option value="line">Grafik Garis (Tren Tahunan 2021–2026)</option>
                    </select>
                </div>
                <div id="td-chart-tahun-wrap">
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--gray-600); display: block; margin-bottom: 3px;">Tahun:</label>
                    <select id="td-chart-tahun-select" class="td-chart-select"></select>
                </div>
            </div>
            <div>
                <button type="button" id="td-btn-unduh-chart" class="td-btn td-btn-outline" style="font-size: 0.8rem;" title="Unduh gambar grafik dalam format PNG">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <span>Unduh Grafik (PNG)</span>
                </button>
            </div>
        </div>
        <div class="td-chart-canvas-container">
            <canvas id="td-chart-canvas"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    'use strict';

    // 1. Bangun Alamat API dari Prefix Config (Tidak di-hardcode)
    const ROUTE_PREFIX = '{{ trim(config("tabel_dinamis.prefix", "tabel-dinamis"), "/") }}';
    const API_BASE = (ROUTE_PREFIX === '' ? '' : '/' + ROUTE_PREFIX) + '/api';

    // Ambil Token CSRF dari meta tag atau cookie XSRF-TOKEN
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) return meta.content;
        const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }
    const CSRF_TOKEN = getCsrfToken();
    const MAKS_DATA = {{ config('tabel_dinamis.maks_data_per_tabel') !== null ? (int)config('tabel_dinamis.maks_data_per_tabel') : 'null' }};
    const TAHUN_MIN = {{ config('tabel_dinamis.tahun_min', 2021) }};
    const TAHUN_MAX = {{ config('tabel_dinamis.tahun_max', 2026) }};

    // Palet warna Deep Blue KINERA untuk chart
    const PALET_WARNA = [
        '#1d4ed8', '#0284c7', '#059669', '#d97706', '#dc2626', 
        '#7c3aed', '#db2777', '#0891b2', '#4b5563', '#2563eb',
        '#10b981', '#f59e0b'
    ];

    // State Manajemen Klien
    let state = {
        subjek: [],
        indikator: [],
        wilayah: [],
        dataTerpilih: [], // { indikator_id, kode, label, satuan, jenis, tahun: [] }
        lastPayload: null,
        tabelRaw: null,
        sortCol: { colType: null, gIdx: null, tIdx: null, dir: null },
        filterWilayahQuery: '',
        activeTab: 'tabel', // 'tabel' | 'grafik'
        chartInstance: null,
        selectedChartGrupIdx: 0,
        selectedChartTipe: 'bar',
        selectedChartTahun: null
    };

    // Referensi Elemen DOM
    const dom = {
        alertBox: document.getElementById('td-alert-box'),
        subjek: document.getElementById('td-subjek'),
        cariIndikator: document.getElementById('td-cari-indikator'),
        indikator: document.getElementById('td-indikator'),
        infoIndikator: document.getElementById('td-info-indikator'),
        jenis: document.getElementById('td-jenis'),
        tahunList: document.getElementById('td-tahun-list'),
        btnTambah: document.getElementById('td-btn-tambah'),
        dataCounter: document.getElementById('td-data-counter'),
        dataTerpilih: document.getElementById('td-data-terpilih'),
        terpilihKosong: document.getElementById('td-terpilih-kosong'),
        semuaWilayah: document.getElementById('td-semua-wilayah'),
        wilayahList: document.getElementById('td-wilayah-list'),
        btnSubmit: document.getElementById('td-btn-submit'),
        btnReset: document.getElementById('td-btn-reset'),
        hasilWrapper: document.getElementById('td-hasil-wrapper'),
        cariTabel: document.getElementById('td-cari-tabel'),
        tableRowCount: document.getElementById('td-table-row-count'),
        tableHead: document.getElementById('td-table-head'),
        tableBody: document.getElementById('td-table-body'),
        tableFoot: document.getElementById('td-table-foot'),
        btnCetak: document.getElementById('td-btn-cetak'),
        exportXlsx: document.getElementById('td-export-xlsx'),
        exportCsv: document.getElementById('td-export-csv'),
        // DOM Switcher & Grafik
        switchTabel: document.getElementById('td-switch-tabel'),
        switchGrafik: document.getElementById('td-switch-grafik'),
        viewTabel: document.getElementById('td-view-tabel'),
        viewGrafik: document.getElementById('td-view-grafik'),
        tabelToolbar: document.getElementById('td-tabel-toolbar'),
        chartIndikatorSelect: document.getElementById('td-chart-indikator-select'),
        chartTipeSelect: document.getElementById('td-chart-tipe-select'),
        chartTahunSelect: document.getElementById('td-chart-tahun-select'),
        chartTahunWrap: document.getElementById('td-chart-tahun-wrap'),
        btnUnduhChart: document.getElementById('td-btn-unduh-chart'),
        chartCanvas: document.getElementById('td-chart-canvas')
    };

    // Helper Tampilkan Pesan Peringatan (Floating Toast Notification)
    let toastTimer = null;
    function tampilkanPesan(pesan, tipe = 'error') {
        clearTimeout(toastTimer);
        const icon = tipe === 'success' ? '✓' : (tipe === 'warning' ? '⚠' : '✕');
        dom.alertBox.innerHTML = `
            <div class="td-alert td-alert-${tipe}">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <span style="font-size: 1rem; line-height: 1; font-weight: bold;">${icon}</span>
                    <span>${pesan}</span>
                </div>
                <button type="button" onclick="this.closest('.td-alert').remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-size:1.1rem;padding:0 0.25rem;opacity:0.75;" title="Tutup">✕</button>
            </div>
        `;
        toastTimer = setTimeout(() => {
            bersihkanPesan();
        }, 4500);
    }

    function bersihkanPesan() {
        dom.alertBox.innerHTML = '';
    }

    // Helper Debounce
    function debounce(func, delay = 350) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Helper Permintaan API dengan CSRF Header pada POST
    async function requestApi(endpoint, options = {}) {
        const defaultHeaders = {
            'Accept': 'application/json'
        };

        // Sertakan token CSRF dinamis jika ada
        const token = getCsrfToken();
        if (token) {
            defaultHeaders['X-CSRF-TOKEN'] = token;
        }

        options.headers = { ...defaultHeaders, ...options.headers };
        if (!options.credentials) {
            options.credentials = 'same-origin';
        }

        const response = await fetch(`${API_BASE}${endpoint}`, options);
        if (!response.ok) {
            let pesanKesalahan = 'Terjadi gangguan komunikasi dengan server.';
            if (response.status === 419) {
                pesanKesalahan = 'Sesi Anda telah berakhir. Silakan muat ulang halaman (F5) untuk memperbarui data.';
            } else {
                try {
                    const jsonErr = await response.json();
                    if (jsonErr.message) {
                        if (jsonErr.message.toLowerCase().includes('csrf')) {
                            pesanKesalahan = 'Sesi Anda telah berakhir. Silakan muat ulang halaman (F5) untuk memperbarui data.';
                        } else {
                            pesanKesalahan = jsonErr.message;
                        }
                    }
                    if (jsonErr.errors) {
                        const firstErr = Object.values(jsonErr.errors)[0];
                        if (Array.isArray(firstErr)) pesanKesalahan = firstErr[0];
                    }
                } catch (e) {}
            }
            throw new Error(pesanKesalahan);
        }
        return response.json();
    }

    // 1. Inisialisasi: Ambil Subjek & Wilayah
    async function inisialisasi() {
        try {
            const [dataSubjek, dataWilayah] = await Promise.all([
                requestApi('/subjek'),
                requestApi('/wilayah')
            ]);

            state.subjek = dataSubjek;
            state.wilayah = dataWilayah;

            renderOpsiSubjek();
            renderCentangWilayah();
        } catch (err) {
            tampilkanPesan(`Gagal memuat data referensi: ${err.message}`);
        }
    }

    function renderOpsiSubjek() {
        dom.subjek.innerHTML = '<option value="">— Pilih Subjek / Sektor —</option>';
        state.subjek.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama;
            dom.subjek.appendChild(opt);
        });
    }

    function renderCentangWilayah() {
        dom.wilayahList.innerHTML = '';
        state.wilayah.forEach(w => {
            const label = document.createElement('label');
            label.className = 'td-checkbox-pill';
            label.innerHTML = `
                <input type="checkbox" class="td-cb-wil" value="${w.id}" checked>
                <span>${w.nama}</span>
            `;
            dom.wilayahList.appendChild(label);
        });

        document.querySelectorAll('.td-cb-wil').forEach(cb => {
            cb.addEventListener('change', sinkronisasiCentangSemuaWilayah);
        });
    }

    function sinkronisasiCentangSemuaWilayah() {
        const total = document.querySelectorAll('.td-cb-wil').length;
        const checked = document.querySelectorAll('.td-cb-wil:checked').length;
        dom.semuaWilayah.checked = (total > 0 && total === checked);
        dom.semuaWilayah.indeterminate = (checked > 0 && checked < total);
    }

    dom.semuaWilayah.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.td-cb-wil').forEach(cb => {
            cb.checked = isChecked;
        });
    });

    // 2. Subjek Dipilih -> Muat Indikator
    dom.subjek.addEventListener('change', async function() {
        const subjekId = this.value;
        dom.cariIndikator.value = '';
        dom.cariIndikator.disabled = !subjekId;
        dom.indikator.innerHTML = '<option value="">— Memuat Indikator... —</option>';
        dom.indikator.disabled = true;
        dom.jenis.disabled = true;
        dom.btnTambah.disabled = true;
        dom.infoIndikator.textContent = '';
        dom.tahunList.innerHTML = '<span style="color: var(--gray-400); font-size: 0.8rem;">Pilih indikator terlebih dahulu.</span>';

        if (!subjekId) {
            dom.indikator.innerHTML = '<option value="">— Pilih Subjek Terlebih Dahulu —</option>';
            return;
        }

        await muatDaftarIndikator(subjekId, '');
    });

    // 3. Pencarian Indikator dengan Debounce
    const debouncedCari = debounce(async function(keyword) {
        const subjekId = dom.subjek.value;
        if (!subjekId) return;
        await muatDaftarIndikator(subjekId, keyword);
    }, 350);

    dom.cariIndikator.addEventListener('input', function() {
        debouncedCari(this.value.trim());
    });

    async function muatDaftarIndikator(subjekId, keyword) {
        try {
            const url = `/indikator?subjek_id=${encodeURIComponent(subjekId)}` + 
                (keyword ? `&q=${encodeURIComponent(keyword)}` : '');
            const indikators = await requestApi(url);
            state.indikator = indikators;

            dom.indikator.innerHTML = '<option value="">— Pilih Indikator —</option>';
            if (indikators.length === 0) {
                dom.indikator.innerHTML = '<option value="">— Tidak ada indikator yang cocok —</option>';
            } else {
                indikators.forEach(ind => {
                    const opt = document.createElement('option');
                    opt.value = ind.id;
                    opt.textContent = `${ind.kode} — ${ind.label}`;
                    dom.indikator.appendChild(opt);
                });
            }
            dom.indikator.disabled = false;
        } catch (err) {
            tampilkanPesan(`Gagal memuat indikator: ${err.message}`);
        }
    }

    // 4. Indikator atau Jenis Dipilih -> Ambil Ketersediaan Tahun
    dom.indikator.addEventListener('change', sinkronisasiIndikatorDanJenis);
    dom.jenis.addEventListener('change', sinkronisasiIndikatorDanJenis);

    async function sinkronisasiIndikatorDanJenis() {
        const indId = dom.indikator.value;
        const jenisVal = dom.jenis.value;

        if (!indId) {
            dom.jenis.disabled = true;
            dom.btnTambah.disabled = true;
            dom.infoIndikator.textContent = '';
            dom.tahunList.innerHTML = '<span style="color: var(--gray-400); font-size: 0.8rem;">Pilih indikator terlebih dahulu.</span>';
            return;
        }

        dom.jenis.disabled = false;
        const terpilih = state.indikator.find(i => i.id == indId);
        if (terpilih) {
            const meta = [];
            if (terpilih.opd) {
                meta.push(`<span class="td-meta-pill td-meta-opd"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 7v14M21 7v14M6 11v6M10 11v6M14 11v6M18 11v6M12 3L2 7h20L12 3z"/></svg> OPD: ${terpilih.opd}</span>`);
            }
            if (terpilih.satuan) {
                meta.push(`<span class="td-meta-pill td-meta-satuan"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Satuan: ${terpilih.satuan}</span>`);
            }
            dom.infoIndikator.innerHTML = `<div class="td-meta-pills-wrap">${meta.join('')}</div>`;
        }

        dom.tahunList.innerHTML = '<span style="color: var(--gray-400); font-size: 0.8rem;">Memeriksa ketersediaan tahun...</span>';

        try {
            const tahunTersedia = await requestApi(`/tahun?indikator_id=${indId}&jenis=${jenisVal}`);
            renderCentangTahun(tahunTersedia);
            periksaStatusTombolTambah();
        } catch (err) {
            renderCentangTahun([]);
            periksaStatusTombolTambah();
        }
    }

    function renderCentangTahun(tahunTersedia) {
        dom.tahunList.innerHTML = '';
        for (let th = TAHUN_MIN; th <= TAHUN_MAX; th++) {
            const adaData = Array.isArray(tahunTersedia) && tahunTersedia.includes(th);
            const label = document.createElement('label');
            label.className = 'td-checkbox-pill';
            label.title = adaData ? `Data tahun ${th} tersedia di sistem` : `Data tahun ${th} belum ada data (nihil)`;
            label.innerHTML = `
                <input type="checkbox" class="td-cb-thn" value="${th}" ${adaData ? 'checked' : ''}>
                <span>${th}</span>
                ${adaData ? '<span class="td-badge-available">Tersedia</span>' : '<span class="td-badge-empty">Nihil</span>'}
            `;
            dom.tahunList.appendChild(label);
        }

        document.querySelectorAll('.td-cb-thn').forEach(cb => {
            cb.addEventListener('change', periksaStatusTombolTambah);
        });
    }

    function periksaStatusTombolTambah() {
        const indId = dom.indikator.value;
        const thnChecked = document.querySelectorAll('.td-cb-thn:checked').length;
        const penuh = MAKS_DATA ? state.dataTerpilih.length >= MAKS_DATA : false;
        dom.btnTambah.disabled = (!indId || thnChecked === 0 || penuh);
    }

    // 5. Tambah ke Data Terpilih
    dom.btnTambah.addEventListener('click', function() {
        bersihkanPesan();

        if (MAKS_DATA && state.dataTerpilih.length >= MAKS_DATA) {
            tampilkanPesan(`Hanya dapat memilih maksimal ${MAKS_DATA} data`, 'warning');
            return;
        }

        const indId = parseInt(dom.indikator.value, 10);
        const itemInd = state.indikator.find(i => i.id == indId);
        const jenisVal = dom.jenis.value;
        const thnDipilih = Array.from(document.querySelectorAll('.td-cb-thn:checked'))
            .map(cb => parseInt(cb.value, 10))
            .sort((a, b) => a - b);

        if (!itemInd || thnDipilih.length === 0) {
            tampilkanPesan('Pilih minimal satu tahun.', 'warning');
            return;
        }

        // Cek duplikasi indikator + jenis
        const duplikat = state.dataTerpilih.find(d => d.indikator_id === indId && d.jenis === jenisVal);
        if (duplikat) {
            tampilkanPesan(`Indikator "${itemInd.label}" dengan jenis "${jenisVal}" sudah ada dalam Data Terpilih.`, 'warning');
            return;
        }

        state.dataTerpilih.push({
            indikator_id: indId,
            kode: itemInd.kode,
            label: itemInd.label,
            satuan: itemInd.satuan || null,
            jenis: jenisVal,
            tahun: thnDipilih
        });

        renderDataTerpilih();
        periksaStatusTombolTambah();
    });

    function renderDataTerpilih() {
        dom.dataTerpilih.innerHTML = '';
        dom.dataCounter.textContent = MAKS_DATA 
            ? `(${state.dataTerpilih.length} / ${MAKS_DATA})` 
            : `(${state.dataTerpilih.length} data)`;

        if (state.dataTerpilih.length === 0) {
            dom.dataTerpilih.appendChild(dom.terpilihKosong);
            dom.btnSubmit.disabled = true;
            return;
        }

        dom.btnSubmit.disabled = false;

        state.dataTerpilih.forEach((item, index) => {
            const box = document.createElement('div');
            box.className = 'td-selected-item';
            box.innerHTML = `
                <div style="flex: 1;">
                    <div class="td-selected-title">[${item.jenis}] ${item.label}</div>
                    <div class="td-selected-meta">Tahun: <strong>${item.tahun.join(', ')}</strong> ${item.satuan ? `· ${item.satuan}` : ''}</div>
                </div>
                <button type="button" class="td-btn-delete" title="Hapus Data" data-index="${index}">✕</button>
            `;
            dom.dataTerpilih.appendChild(box);
        });

        // Event listener hapus
        dom.dataTerpilih.querySelectorAll('.td-btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                state.dataTerpilih.splice(idx, 1);
                renderDataTerpilih();
                periksaStatusTombolTambah();
            });
        });
    }

    // 6. Tombol Atur Ulang (Reset)
    dom.btnReset.addEventListener('click', function() {
        state.dataTerpilih = [];
        state.lastPayload = null;
        renderDataTerpilih();
        periksaStatusTombolTambah();
        dom.hasilWrapper.classList.add('td-hidden');
        bersihkanPesan();
    });

    // 7. Tombol Submit: Kirim Permintaan Tabel Dinamis via POST dengan CSRF
    dom.btnSubmit.addEventListener('click', async function() {
        bersihkanPesan();

        if (state.dataTerpilih.length === 0) {
            tampilkanPesan('Pilih minimal 1 data indikator sebelum submit.', 'warning');
            return;
        }

        if (MAKS_DATA && state.dataTerpilih.length > MAKS_DATA) {
            tampilkanPesan(`Hanya dapat memilih maksimal ${MAKS_DATA} data`, 'warning');
            return;
        }

        const checkedWil = Array.from(document.querySelectorAll('.td-cb-wil:checked'));
        if (checkedWil.length === 0) {
            tampilkanPesan('Pilih minimal 1 wilayah.', 'warning');
            return;
        }

        const wilayahIds = (dom.semuaWilayah.checked && checkedWil.length === state.wilayah.length)
            ? null
            : checkedWil.map(cb => parseInt(cb.value, 10));

        const payload = {
            wilayah_ids: wilayahIds,
            data: state.dataTerpilih.map(d => ({
                indikator_id: d.indikator_id,
                jenis: d.jenis,
                tahun: d.tahun
            }))
        };

        dom.btnSubmit.disabled = true;
        dom.btnSubmit.innerHTML = '<span class="td-spinner"></span> Memuat...';

        try {
            const hasilTabel = await requestApi('/tabel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            state.lastPayload = payload;
            renderTabelDuaTingkat(hasilTabel);
            dom.hasilWrapper.classList.remove('td-hidden');
            dom.hasilWrapper.scrollIntoView({ behavior: 'smooth' });
        } catch (err) {
            tampilkanPesan(err.message, 'error');
        } finally {
            dom.btnSubmit.disabled = false;
            dom.btnSubmit.innerHTML = 'Tampilkan Tabel';
        }
    });

    // 8. Render Tabel Header Dua Tingkat, Sorting, Search, dan Ringkasan Statistik
    const formatIndo = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 4
    });

    function renderTabelDuaTingkat(hasilTabel) {
        state.tabelRaw = hasilTabel;
        state.sortCol = { colType: null, gIdx: null, tIdx: null, dir: null };
        state.filterWilayahQuery = '';
        if (dom.cariTabel) dom.cariTabel.value = '';

        renderTabelHeader();
        updateTabelRowsAndStats();
        syncChartControls();

        if (state.activeTab === 'grafik') {
            renderGrafik();
        }
    }

    function renderTabelHeader() {
        if (!state.tabelRaw) return;
        const { grup } = state.tabelRaw;

        // Baris Header 1 & Baris Header 2 (Header Dua Tingkat)
        let wilSortIcon = '↕';
        let wilActive = '';
        if (state.sortCol.colType === 'wilayah') {
            wilSortIcon = state.sortCol.dir === 'asc' ? '▲' : '▼';
            wilActive = 'td-sort-active';
        }

        let thTingkat1 = `<tr><th rowspan="2" class="td-th-wilayah td-sortable-th ${wilActive}" id="td-th-sort-wilayah" title="Klik untuk mengurutkan nama wilayah">Kabupaten / Kota <span class="td-sort-icon">${wilSortIcon}</span></th>`;
        let thTingkat2 = '<tr>';

        grup.forEach((g, gIdx) => {
            const colSpan = g.tahun.length;
            const satuanTeks = g.satuan ? ` (${g.satuan})` : '';
            const tagClass = g.jenis.toLowerCase() === 'target' ? 'td-tag-target' : 'td-tag-realisasi';
            thTingkat1 += `<th colspan="${colSpan}"><span class="td-table-tag ${tagClass}">[${g.jenis}]</span> ${g.judul}${satuanTeks}</th>`;

            g.tahun.forEach((th, tIdx) => {
                let mSortIcon = '↕';
                let mActive = '';
                if (state.sortCol.colType === 'metric' && state.sortCol.gIdx === gIdx && state.sortCol.tIdx === tIdx) {
                    mSortIcon = state.sortCol.dir === 'asc' ? '▲' : '▼';
                    mActive = 'td-sort-active';
                }
                thTingkat2 += `<th style="min-width: 85px;" class="td-sortable-th ${mActive}" data-gidx="${gIdx}" data-tidx="${tIdx}" title="Klik untuk mengurutkan nilai ${th}">${th} <span class="td-sort-icon">${mSortIcon}</span></th>`;
            });
        });

        thTingkat1 += '</tr>';
        thTingkat2 += '</tr>';
        dom.tableHead.innerHTML = thTingkat1 + thTingkat2;

        // Pasang event listener sorting
        const thWil = document.getElementById('td-th-sort-wilayah');
        if (thWil) {
            thWil.addEventListener('click', () => toggleSort('wilayah'));
        }

        dom.tableHead.querySelectorAll('.td-sortable-th[data-gidx]').forEach(th => {
            th.addEventListener('click', function() {
                const gIdx = parseInt(this.getAttribute('data-gidx'), 10);
                const tIdx = parseInt(this.getAttribute('data-tidx'), 10);
                toggleSort('metric', gIdx, tIdx);
            });
        });
    }

    function toggleSort(type, gIdx = null, tIdx = null) {
        if (type === 'wilayah') {
            if (state.sortCol.colType === 'wilayah') {
                state.sortCol.dir = state.sortCol.dir === 'asc' ? 'desc' : 'asc';
            } else {
                state.sortCol = { colType: 'wilayah', gIdx: null, tIdx: null, dir: 'asc' };
            }
        } else {
            if (state.sortCol.colType === 'metric' && state.sortCol.gIdx === gIdx && state.sortCol.tIdx === tIdx) {
                state.sortCol.dir = state.sortCol.dir === 'asc' ? 'desc' : 'asc';
            } else {
                state.sortCol = { colType: 'metric', gIdx, tIdx, dir: 'desc' }; // Metric default desc (nilai tertinggi di atas)
            }
        }
        renderTabelHeader();
        updateTabelRowsAndStats();
    }

    function updateTabelRowsAndStats() {
        if (!state.tabelRaw) return;
        const { grup, baris } = state.tabelRaw;

        // 1. Filter baris wilayah
        let filtered = (baris || []).slice();
        if (state.filterWilayahQuery) {
            filtered = filtered.filter(b => b.wilayah.toLowerCase().includes(state.filterWilayahQuery));
        }

        // 2. Urutkan baris
        if (state.sortCol.colType === 'wilayah') {
            filtered.sort((a, b) => {
                const cmp = a.wilayah.localeCompare(b.wilayah, 'id');
                return state.sortCol.dir === 'asc' ? cmp : -cmp;
            });
        } else if (state.sortCol.colType === 'metric') {
            const g = state.sortCol.gIdx;
            const t = state.sortCol.tIdx;
            filtered.sort((a, b) => {
                const valA = a.nilai[g]?.[t] ?? null;
                const valB = b.nilai[g]?.[t] ?? null;
                if (valA === null && valB === null) return 0;
                if (valA === null) return 1; // null selalu di urutan paling bawah
                if (valB === null) return -1;
                return state.sortCol.dir === 'asc' ? (valA - valB) : (valB - valA);
            });
        }

        // Update indikator jumlah baris
        if (dom.tableRowCount) {
            if (filtered.length !== (baris ? baris.length : 0)) {
                dom.tableRowCount.textContent = `${filtered.length} dari ${baris.length} wilayah`;
            } else {
                dom.tableRowCount.textContent = `${baris.length} wilayah`;
            }
        }

        // 3. Render tbody
        if (filtered.length === 0) {
            dom.tableBody.innerHTML = `<tr><td colspan="100" class="td-align-center" style="padding: 2rem;">Tidak ada wilayah yang cocok dengan pencarian "${state.filterWilayahQuery}".</td></tr>`;
            if (dom.tableFoot) dom.tableFoot.innerHTML = '';
            return;
        }

        let bodyHtml = '';
        filtered.forEach(b => {
            bodyHtml += `<tr><td class="td-td-wilayah">${b.wilayah}</td>`;
            b.nilai.forEach(grupNilai => {
                grupNilai.forEach(val => {
                    if (val === null || val === undefined) {
                        bodyHtml += `<td class="td-align-center">-</td>`;
                    } else {
                        bodyHtml += `<td class="td-align-right">${formatIndo.format(val)}</td>`;
                    }
                });
            });
            bodyHtml += '</tr>';
        });
        dom.tableBody.innerHTML = bodyHtml;

        // 4. Hitung Ringkasan Statistik tfoot (Rata-rata, Tertinggi, Terendah)
        if (dom.tableFoot) {
            let statCellsAvg = '';
            let statCellsMax = '';
            let statCellsMin = '';

            grup.forEach((g, gIdx) => {
                g.tahun.forEach((th, tIdx) => {
                    const values = [];
                    filtered.forEach(b => {
                        const v = b.nilai[gIdx]?.[tIdx];
                        if (v !== null && v !== undefined && !isNaN(v)) {
                            values.push(Number(v));
                        }
                    });

                    if (values.length === 0) {
                        statCellsAvg += `<td class="td-align-center">-</td>`;
                        statCellsMax += `<td class="td-align-center">-</td>`;
                        statCellsMin += `<td class="td-align-center">-</td>`;
                    } else {
                        const sum = values.reduce((acc, cur) => acc + cur, 0);
                        const avg = sum / values.length;
                        const max = Math.max(...values);
                        const min = Math.min(...values);

                        statCellsAvg += `<td class="td-align-right" style="font-weight:700;">${formatIndo.format(avg)}</td>`;
                        statCellsMax += `<td class="td-align-right" style="font-weight:700;">${formatIndo.format(max)}</td>`;
                        statCellsMin += `<td class="td-align-right" style="font-weight:700;">${formatIndo.format(min)}</td>`;
                    }
                });
            });

            dom.tableFoot.innerHTML = `
                <tr class="td-tfoot-avg">
                    <td class="td-td-wilayah" style="font-weight:700;">Rata-rata Provinsi</td>
                    ${statCellsAvg}
                </tr>
                <tr class="td-tfoot-max">
                    <td class="td-td-wilayah" style="font-weight:700;">Tertinggi (Maks)</td>
                    ${statCellsMax}
                </tr>
                <tr class="td-tfoot-min">
                    <td class="td-td-wilayah" style="font-weight:700;">Terendah (Min)</td>
                    ${statCellsMin}
                </tr>
            `;
        }
    }

    // Event Listener Live Search Wilayah di Tabel
    if (dom.cariTabel) {
        dom.cariTabel.addEventListener('input', function() {
            state.filterWilayahQuery = this.value.trim().toLowerCase();
            updateTabelRowsAndStats();
        });
    }

    // Event Listener Tombol Cetak / PDF Resmi
    if (dom.btnCetak) {
        dom.btnCetak.addEventListener('click', function() {
            window.print();
        });
    }

    // ===== Tab Switching (Tabel Data vs Visualisasi Grafik) =====
    function setTab(tab) {
        state.activeTab = tab;
        if (tab === 'tabel') {
            dom.switchTabel.classList.add('active');
            dom.switchGrafik.classList.remove('active');
            dom.viewTabel.classList.remove('td-hidden');
            dom.viewGrafik.classList.add('td-hidden');
            if (dom.tabelToolbar) dom.tabelToolbar.classList.remove('td-hidden');
        } else {
            dom.switchGrafik.classList.add('active');
            dom.switchTabel.classList.remove('active');
            dom.viewGrafik.classList.remove('td-hidden');
            dom.viewTabel.classList.add('td-hidden');
            if (dom.tabelToolbar) dom.tabelToolbar.classList.add('td-hidden');
            renderGrafik();
        }
    }

    if (dom.switchTabel) dom.switchTabel.addEventListener('click', () => setTab('tabel'));
    if (dom.switchGrafik) dom.switchGrafik.addEventListener('click', () => setTab('grafik'));

    // Inisialisasi Kontrol Dropdown Grafik
    function syncChartControls() {
        if (!state.tabelRaw || !state.tabelRaw.grup || state.tabelRaw.grup.length === 0) return;

        // 1. Opsi Indikator
        if (dom.chartIndikatorSelect) {
            dom.chartIndikatorSelect.innerHTML = '';
            state.tabelRaw.grup.forEach((g, idx) => {
                const opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = `[${g.jenis}] ${g.judul}${g.satuan ? ' (' + g.satuan + ')' : ''}`;
                dom.chartIndikatorSelect.appendChild(opt);
            });
            state.selectedChartGrupIdx = 0;
            dom.chartIndikatorSelect.value = '0';
        }

        // 2. Opsi Tahun
        updateChartTahunOptions();
    }

    function updateChartTahunOptions() {
        if (!dom.chartTahunSelect) return;
        const g = state.tabelRaw?.grup?.[state.selectedChartGrupIdx];
        if (!g) return;

        dom.chartTahunSelect.innerHTML = '';
        g.tahun.forEach(th => {
            const opt = document.createElement('option');
            opt.value = th;
            opt.textContent = `Tahun ${th}`;
            dom.chartTahunSelect.appendChild(opt);
        });

        // Default ke tahun paling baru yang ada
        const defaultTh = g.tahun[g.tahun.length - 1];
        state.selectedChartTahun = defaultTh;
        dom.chartTahunSelect.value = defaultTh;
    }

    if (dom.chartIndikatorSelect) {
        dom.chartIndikatorSelect.addEventListener('change', function() {
            state.selectedChartGrupIdx = parseInt(this.value, 10);
            updateChartTahunOptions();
            renderGrafik();
        });
    }

    if (dom.chartTipeSelect) {
        dom.chartTipeSelect.addEventListener('change', function() {
            state.selectedChartTipe = this.value;
            renderGrafik();
        });
    }

    if (dom.chartTahunSelect) {
        dom.chartTahunSelect.addEventListener('change', function() {
            state.selectedChartTahun = parseInt(this.value, 10);
            renderGrafik();
        });
    }

    // Fungsi Utama Render Grafik Menggunakan Chart.js
    function renderGrafik() {
        if (!state.tabelRaw || typeof Chart === 'undefined' || !dom.chartCanvas) return;
        const { grup, baris } = state.tabelRaw;
        const gIdx = state.selectedChartGrupIdx;
        const g = grup?.[gIdx];
        if (!g) return;

        if (state.chartInstance) {
            state.chartInstance.destroy();
            state.chartInstance = null;
        }

        const tipe = state.selectedChartTipe;
        const ctx = dom.chartCanvas.getContext('2d');
        const satuanLabel = g.satuan ? ` (${g.satuan})` : '';

        if (tipe === 'bar') {
            if (dom.chartTahunWrap) dom.chartTahunWrap.style.display = 'block';
            const tIdx = g.tahun.indexOf(parseInt(state.selectedChartTahun, 10));
            const safeTIdx = tIdx >= 0 ? tIdx : 0;
            const tahunAktif = g.tahun[safeTIdx];

            const labels = (baris || []).map(b => b.wilayah);
            const values = (baris || []).map(b => b.nilai[gIdx]?.[safeTIdx] ?? null);

            state.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `[${g.jenis}] ${g.judul} — ${tahunAktif}${satuanLabel}`,
                        data: values,
                        backgroundColor: labels.map((_, i) => PALET_WARNA[i % PALET_WARNA.length] + 'cc'),
                        borderColor: labels.map((_, i) => PALET_WARNA[i % PALET_WARNA.length]),
                        borderWidth: 1.5,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw;
                                    return val === null ? 'Data Nihil (-)' : `${formatIndo.format(val)}${satuanLabel}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                                maxRotation: 40,
                                minRotation: 15
                            },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: val => formatIndo.format(val),
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                            },
                            grid: { color: 'rgba(15, 34, 64, 0.06)' }
                        }
                    }
                }
            });
        } else {
            // Line Chart (Tren Waktu 2021–2026)
            if (dom.chartTahunWrap) dom.chartTahunWrap.style.display = 'none';
            const labels = g.tahun.map(t => `${t}`);
            const datasets = [];

            // Garis rata-rata provinsi (paling kontras di atas)
            const avgData = g.tahun.map((_, tIdx) => {
                const vals = [];
                (baris || []).forEach(b => {
                    const v = b.nilai[gIdx]?.[tIdx];
                    if (v !== null && v !== undefined && !isNaN(v)) vals.push(Number(v));
                });
                return vals.length > 0 ? (vals.reduce((a, c) => a + c, 0) / vals.length) : null;
            });

            datasets.push({
                label: 'Rata-rata Provinsi Maluku Utara',
                data: avgData,
                borderColor: '#0f2240',
                backgroundColor: '#0f2240',
                borderWidth: 3.5,
                borderDash: [6, 4],
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.2,
                fill: false
            });

            // Garis per wilayah
            (baris || []).forEach((b, bIdx) => {
                const dataPoin = g.tahun.map((_, tIdx) => b.nilai[gIdx]?.[tIdx] ?? null);
                const color = PALET_WARNA[bIdx % PALET_WARNA.length];
                datasets.push({
                    label: b.wilayah,
                    data: dataPoin,
                    borderColor: color,
                    backgroundColor: color + '22',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.25,
                    fill: false
                });
            });

            state.chartInstance = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw;
                                    return `${context.dataset.label}: ${val === null ? '-' : formatIndo.format(val)}${satuanLabel}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' }
                            },
                            grid: { color: 'rgba(15, 34, 64, 0.04)' }
                        },
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: val => formatIndo.format(val),
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                            },
                            grid: { color: 'rgba(15, 34, 64, 0.06)' }
                        }
                    }
                }
            });
        }
    }

    // Unduh Gambar Grafik PNG
    if (dom.btnUnduhChart) {
        dom.btnUnduhChart.addEventListener('click', function() {
            if (!state.chartInstance) {
                tampilkanPesan('Belum ada grafik yang siap diunduh.', 'warning');
                return;
            }
            const link = document.createElement('a');
            link.download = `grafik-kinera-${new Date().toISOString().slice(0,10)}.png`;
            link.href = state.chartInstance.toBase64Image('image/png', 1.0);
            link.click();
            tampilkanPesan('Gambar grafik berhasil diunduh.', 'success');
        });
    }

    // 9. Handler Unduh Berkas Export (POST dengan CSRF)
    async function unduhBerkasExport(format) {
        if (!state.lastPayload) {
            tampilkanPesan('Silakan lakukan submit tabel terlebih dahulu.', 'warning');
            return;
        }

        const btn = format === 'xlsx' ? dom.exportXlsx : dom.exportCsv;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="td-spinner" style="width:0.75rem;height:0.75rem;margin-right:0.3rem;"></span> Mengunduh...`;

        try {
            const response = await fetch(`${API_BASE}/export?format=${format}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/octet-stream, application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify(state.lastPayload)
            });

            if (!response.ok) {
                let errPesan = 'Gagal mengunduh berkas.';
                if (response.status === 419) {
                    errPesan = 'Sesi Anda telah berakhir. Silakan muat ulang halaman (F5) untuk memperbarui data.';
                } else {
                    try {
                        const errObj = await response.json();
                        if (errObj.message) {
                            if (errObj.message.toLowerCase().includes('csrf')) {
                                errPesan = 'Sesi Anda telah berakhir. Silakan muat ulang halaman (F5) untuk memperbarui data.';
                            } else {
                                errPesan = errObj.message;
                            }
                        }
                    } catch(e) {}
                }
                throw new Error(errPesan);
            }

            const blob = await response.blob();
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = `tabel-dinamis-${new Date().toISOString().slice(0,10)}.${format}`;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(link.href);
            tampilkanPesan(`Berkas ${format.toUpperCase()} berhasil diunduh.`, 'success');
        } catch (err) {
            tampilkanPesan(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    }

    dom.exportXlsx.addEventListener('click', () => unduhBerkasExport('xlsx'));
    dom.exportCsv.addEventListener('click', () => unduhBerkasExport('csv'));

    // Jalankan
    inisialisasi();
})();
</script>
@endpush

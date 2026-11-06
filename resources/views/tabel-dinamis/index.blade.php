@extends(config('tabel_dinamis.layout'))

@section('title', 'KINERA — Tabel Dinamis Indikator Bidang IPW')

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
        height: 4px;
        background: linear-gradient(90deg, #0d286d 0%, #1d4ed8 40%, #2563eb 75%, #38bdf8 100%);
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
        background: linear-gradient(135deg, #091a38 0%, #153e7e 60%, #1d4ed8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
        transition: all 0.28s ease;
    }
    .td-section-desc {
        font-size: 0.72rem;
        font-weight: 500;
        color: #64748b;
        letter-spacing: 0.01em;
        line-height: 1.2;
    }
    .td-section-box:hover .td-section-title {
        background: linear-gradient(135deg, #071731 0%, #1d4ed8 50%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ===== Step Badge (Luminous 3D Embossed Deep Blue Badge) ===== */
    .td-step-badge {
        background: radial-gradient(circle at 32% 28%, #3b82f6 0%, #1d4ed8 35%, #10398e 70%, #091f54 100%);
        color: #ffffff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        flex-shrink: 0;
        border: 2px solid #ffffff;
        outline: 1.5px solid rgba(29, 78, 216, 0.35);
        box-shadow: 0 4px 10px -1px rgba(9, 31, 84, 0.5),
                    0 2px 4px rgba(9, 31, 84, 0.25),
                    inset 0 2px 2.5px rgba(255, 255, 255, 0.7),
                    inset 0 -2px 3px rgba(3, 12, 38, 0.6);
        text-shadow: 0 1px 2px rgba(3, 12, 38, 0.8);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .td-section-box:hover .td-step-badge {
        transform: translateY(-2px) scale(1.08);
        box-shadow: 0 7px 16px -1px rgba(9, 31, 84, 0.6),
                    0 3px 6px rgba(9, 31, 84, 0.3),
                    inset 0 2px 2.5px rgba(255, 255, 255, 0.85),
                    inset 0 -2px 3px rgba(3, 12, 38, 0.7);
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
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #0f2e6b;
        font-weight: 700;
        box-shadow: 0 3px 8px rgba(29, 78, 216, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.9);
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
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
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
        height: 3.5px;
        background: linear-gradient(90deg, var(--deep-blue-600) 0%, var(--deep-blue-400) 100%);
    }
</style>
@endpush

@section('content')
<div id="td-alert-box"></div>

<!-- Banner Kewajiban Maksimal 2 Data -->
<div class="td-banner-info">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
    <span>Hanya dapat memilih maksimal 2 data per tabel</span>
</div>

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
                <span id="td-data-counter" class="td-counter-badge">(0 / 2)</span>
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
    <div class="td-result-header">
        <div class="td-result-title">
            <span class="td-h2-icon" style="width:28px;height:28px;background:var(--deep-blue-50);border-radius:var(--radius-sm);display:inline-flex;align-items:center;justify-content:center;font-size:0.9rem;">📋</span>
            Tabel Hasil Dinamis
        </div>
        <div class="td-export-group">
            <button type="button" id="td-export-xlsx" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
                📥 Unduh Excel
            </button>
            <button type="button" id="td-export-csv" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
                📄 Unduh CSV
            </button>
        </div>
    </div>

    <!-- Tabel Header Dua Tingkat yang Bisa Digeser Horizontal -->
    <div class="td-table-scroll">
        <table class="td-table-dynamic">
            <thead id="td-table-head"></thead>
            <tbody id="td-table-body"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // 1. Bangun Alamat API dari Prefix Config (Tidak di-hardcode)
    const ROUTE_PREFIX = '{{ trim(config("tabel_dinamis.prefix", "tabel-dinamis"), "/") }}';
    const API_BASE = (ROUTE_PREFIX === '' ? '' : '/' + ROUTE_PREFIX) + '/api';

    // Ambil Token CSRF dari meta tag
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const MAKS_DATA = {{ config('tabel_dinamis.maks_data_per_tabel', 2) }};
    const TAHUN_MIN = {{ config('tabel_dinamis.tahun_min', 2021) }};
    const TAHUN_MAX = {{ config('tabel_dinamis.tahun_max', 2026) }};

    // State Manajemen Klien
    let state = {
        subjek: [],
        indikator: [],
        wilayah: [],
        dataTerpilih: [], // Maksimal 2 item: { indikator_id, kode, label, satuan, jenis, tahun: [] }
        lastPayload: null
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
        tableHead: document.getElementById('td-table-head'),
        tableBody: document.getElementById('td-table-body'),
        exportXlsx: document.getElementById('td-export-xlsx'),
        exportCsv: document.getElementById('td-export-csv')
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

        // Sertakan token CSRF jika ada
        if (CSRF_TOKEN) {
            defaultHeaders['X-CSRF-TOKEN'] = CSRF_TOKEN;
        }

        options.headers = { ...defaultHeaders, ...options.headers };

        const response = await fetch(`${API_BASE}${endpoint}`, options);
        if (!response.ok) {
            let pesanKesalahan = 'Terjadi gangguan komunikasi dengan server.';
            try {
                const jsonErr = await response.json();
                if (jsonErr.message) pesanKesalahan = jsonErr.message;
                if (jsonErr.errors) {
                    const firstErr = Object.values(jsonErr.errors)[0];
                    if (Array.isArray(firstErr)) pesanKesalahan = firstErr[0];
                }
            } catch (e) {}
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
            label.title = adaData ? `Data tahun ${th} tersedia di sistem` : `Data tahun ${th} belum tersedia`;
            label.innerHTML = `
                <input type="checkbox" class="td-cb-thn" value="${th}" ${adaData ? 'checked' : ''}>
                <span>${th}</span>
                ${adaData ? '<span class="td-badge-available">Tersedia</span>' : ''}
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
        const penuh = state.dataTerpilih.length >= MAKS_DATA;
        dom.btnTambah.disabled = (!indId || thnChecked === 0 || penuh);
    }

    // 5. Tambah ke Data Terpilih (Maksimal 2 Data)
    dom.btnTambah.addEventListener('click', function() {
        bersihkanPesan();

        if (state.dataTerpilih.length >= MAKS_DATA) {
            tampilkanPesan('Hanya dapat memilih maksimal 2 data', 'warning');
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
        dom.dataCounter.textContent = `(${state.dataTerpilih.length} / ${MAKS_DATA})`;

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

        if (state.dataTerpilih.length > MAKS_DATA) {
            tampilkanPesan('Hanya dapat memilih maksimal 2 data', 'warning');
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

    // 8. Render Tabel Header Dua Tingkat & Format Angka Indonesia
    function renderTabelDuaTingkat(hasilTabel) {
        const { grup, baris } = hasilTabel;

        // Baris Header 1 & Baris Header 2 (Header Dua Tingkat)
        let thTingkat1 = '<tr><th rowspan="2" class="td-th-wilayah">Kabupaten / Kota</th>';
        let thTingkat2 = '<tr>';

        grup.forEach(g => {
            const colSpan = g.tahun.length;
            const satuanTeks = g.satuan ? ` (${g.satuan})` : '';
            const tagClass = g.jenis.toLowerCase() === 'target' ? 'td-tag-target' : 'td-tag-realisasi';
            thTingkat1 += `<th colspan="${colSpan}"><span class="td-table-tag ${tagClass}">[${g.jenis}]</span> ${g.judul}${satuanTeks}</th>`;

            g.tahun.forEach(th => {
                thTingkat2 += `<th style="min-width: 85px;">${th}</th>`;
            });
        });

        thTingkat1 += '</tr>';
        thTingkat2 += '</tr>';
        dom.tableHead.innerHTML = thTingkat1 + thTingkat2;

        // Render Baris Wilayah dan Format Angka Indonesia
        // Formatter angka standar Indonesia (titik untuk ribuan, koma untuk desimal)
        const formatIndo = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 4
        });

        let bodyHtml = '';
        if (!baris || baris.length === 0) {
            bodyHtml = `<tr><td colspan="100" class="td-align-center" style="padding: 2rem;">Tidak ada data ditemukan.</td></tr>`;
        } else {
            baris.forEach(b => {
                bodyHtml += `<tr><td class="td-td-wilayah">${b.wilayah}</td>`;
                b.nilai.forEach(grupNilai => {
                    grupNilai.forEach(val => {
                        if (val === null || val === undefined) {
                            // Null ditulis '-'
                            bodyHtml += `<td class="td-align-center">-</td>`;
                        } else {
                            // Angka format Indonesia
                            bodyHtml += `<td class="td-align-right">${formatIndo.format(val)}</td>`;
                        }
                    });
                });
                bodyHtml += '</tr>';
            });
        }
        dom.tableBody.innerHTML = bodyHtml;
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
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(state.lastPayload)
            });

            if (!response.ok) {
                let errPesan = 'Gagal mengunduh berkas.';
                try {
                    const errObj = await response.json();
                    if (errObj.message) errPesan = errObj.message;
                } catch(e) {}
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

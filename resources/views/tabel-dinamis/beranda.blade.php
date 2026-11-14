@extends(config('tabel_dinamis.layout'))

@section('title', 'Beranda KINERA — Kinerja Informasi & Data Daerah Bidang IPW')

@push('styles')
<style>
    /* ===== Container Khusus Beranda ===== */
    .td-beranda-wrap {
        max-width: 1140px;
        margin: 0 auto;
        padding-bottom: 2.5rem;
    }

    /* ===== Hero Section ===== */
    .td-hero {
        text-align: center;
        padding: 3.5rem 1.5rem 3rem;
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.75) 0%, rgba(255, 255, 255, 0.95) 100%);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 18px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        animation: tdFadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .td-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1d4ed8 0%, #38bdf8 100%);
    }
    .td-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.3rem 0.9rem;
        border-radius: 9999px;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.08);
    }
    .td-hero-tag .td-dot {
        width: 7px;
        height: 7px;
        background: #1d4ed8;
        border-radius: 50%;
        display: inline-block;
        animation: tdPulseLive 2.2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    .td-hero-title {
        font-size: 2.35rem;
        font-weight: 900;
        letter-spacing: -0.035em;
        color: #0f172a;
        line-height: 1.2;
        margin: 0 auto 1rem;
        max-width: 820px;
    }
    .td-hero-title span {
        color: #1d4ed8;
    }
    .td-hero-desc {
        font-size: 1.025rem;
        color: #475569;
        max-width: 740px;
        margin: 0 auto 2rem;
        line-height: 1.65;
    }
    .td-hero-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.85rem;
    }
    .td-btn-lg {
        padding: 0.75rem 1.45rem;
        font-size: 0.925rem;
        border-radius: 10px;
        font-weight: 700;
    }

    /* ===== Stat Metrics Grid ===== */
    .td-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.15rem;
        margin-bottom: 2.5rem;
    }
    .td-stat-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 14px;
        padding: 1.35rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        animation: tdFadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .td-stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(147, 197, 253, 0.85);
        box-shadow: 0 6px 18px -3px rgba(15, 23, 42, 0.08);
    }
    .td-stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .td-stat-icon-wrap svg {
        width: 24px;
        height: 24px;
    }
    .td-stat-num {
        font-size: 1.65rem;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 0.15rem;
    }
    .td-stat-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* ===== Section Block Styling ===== */
    .td-section-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 16px;
        padding: 2.25rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        position: relative;
    }
    .td-section-head {
        margin-bottom: 1.75rem;
        text-align: center;
    }
    .td-badge-pill {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.5rem;
    }
    .td-section-h2 {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        color: #0f172a;
        margin: 0 0 0.5rem;
    }
    .td-section-sub {
        font-size: 0.925rem;
        color: #64748b;
        max-width: 680px;
        margin: 0 auto;
        line-height: 1.55;
    }

    /* ===== 4 Pilar Tujuan KINERA ===== */
    .td-pillar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
    }
    .td-pillar-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.35rem;
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }
    .td-pillar-item:hover {
        background: #ffffff;
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }
    .td-pillar-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #1d4ed8;
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 800;
        margin-bottom: 0.85rem;
    }
    .td-pillar-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.45rem;
    }
    .td-pillar-desc {
        font-size: 0.825rem;
        color: #475569;
        line-height: 1.5;
        margin: 0;
    }

    /* ===== Fitur Unggulan Grid ===== */
    .td-feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.25rem;
    }
    .td-feature-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .td-feature-item:hover {
        transform: translateY(-2px);
        border-color: #93c5fd;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .td-feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .td-feature-icon svg {
        width: 22px;
        height: 22px;
    }
    .td-feature-content h3 {
        font-size: 0.975rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.35rem;
    }
    .td-feature-content p {
        font-size: 0.825rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    /* ===== OPD Grid ===== */
    .td-opd-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.1rem;
    }
    .td-opd-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.2rem;
        border-left: 4px solid #1d4ed8;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .td-opd-card:hover {
        background: #ffffff;
        border-color: #93c5fd;
        border-left-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .td-opd-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.4rem;
        line-height: 1.35;
    }
    .td-opd-badge {
        font-size: 0.72rem;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        display: inline-block;
        align-self: flex-start;
    }

    /* ===== Workflow Steps (4 Langkah) ===== */
    .td-workflow-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        position: relative;
    }
    .td-workflow-step {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.35rem 1.15rem;
        text-align: center;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .td-workflow-step:hover {
        background: #ffffff;
        border-color: #93c5fd;
        transform: translateY(-2px);
    }
    .td-workflow-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #1d4ed8;
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.85rem;
        box-shadow: 0 2px 8px rgba(29, 78, 216, 0.3);
    }
    .td-workflow-title {
        font-size: 0.925rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.35rem;
    }
    .td-workflow-desc {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.45;
        margin: 0;
    }

    /* ===== Banner CTA Akhir ===== */
    .td-cta-banner {
        background: linear-gradient(135deg, #0f2240 0%, #1e40af 100%);
        border-radius: 16px;
        padding: 2.75rem 2rem;
        color: #ffffff;
        text-align: center;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.25);
        margin-top: 2rem;
    }
    .td-cta-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        margin: 0 0 0.75rem;
        color: #ffffff;
    }
    .td-cta-desc {
        font-size: 0.95rem;
        color: #cbd5e1;
        max-width: 620px;
        margin: 0 auto 1.5rem;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
<div class="td-beranda-wrap">

    <!-- ==================== HERO SECTION ==================== -->
    <section class="td-hero">
        <div class="td-hero-tag">
            <span class="td-dot"></span>
            <span>Sistem Informasi Kinerja Pembangunan Daerah — Bidang IPW</span>
        </div>
        <h1 class="td-hero-title">
            Satu Pintu Eksplorasi & Evaluasi <span>Data Pembangunan</span> Maluku Utara
        </h1>
        <p class="td-hero-desc">
            <strong>KINERA</strong> mengintegrasikan data statistik capaian indikator dari {{ $totalOpd }} Organisasi Perangkat Daerah (OPD) 
            Bidang Infrastruktur dan Pengembangan Wilayah ke dalam tabel dinamis multidimensi, visualisasi analitik interaktif, 
            dan pelaporan resmi Pemerintah Provinsi Maluku Utara.
        </p>
        <div class="td-hero-actions">
            <a href="{{ route('tabel-dinamis.index') }}" class="td-btn td-btn-primary td-btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                <span>Buka Tabel Dinamis</span>
                <span style="margin-left: 0.25rem;">→</span>
            </a>
            <a href="#fitur-unggulan" class="td-btn td-btn-outline td-btn-lg">
                <span>Pelajari Fitur Unggulan</span>
                <span style="margin-left: 0.25rem;">↓</span>
            </a>
        </div>
    </section>

    <!-- ==================== STAT METRICS ROW ==================== -->
    <section class="td-stat-grid">
        <div class="td-stat-card">
            <div class="td-stat-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <div class="td-stat-num">{{ $totalOpd }}</div>
                <div class="td-stat-label">OPD Terintegrasi</div>
            </div>
        </div>
        <div class="td-stat-card">
            <div class="td-stat-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <div class="td-stat-num">{{ $totalIndikator }}</div>
                <div class="td-stat-label">Indikator Kinerja</div>
            </div>
        </div>
        <div class="td-stat-card">
            <div class="td-stat-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <div class="td-stat-num">{{ $totalWilayah }}</div>
                <div class="td-stat-label">Kabupaten / Kota</div>
            </div>
        </div>
        <div class="td-stat-card">
            <div class="td-stat-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h4" />
                </svg>
            </div>
            <div>
                <div class="td-stat-num">{{ number_format($totalData, 0, ',', '.') }}+</div>
                <div class="td-stat-label">Data Target & Realisasi</div>
            </div>
        </div>
    </section>

    <!-- ==================== TUJUAN & LATAR BELAKANG ==================== -->
    <section class="td-section-card">
        <div class="td-section-head">
            <span class="td-badge-pill">Latar Belakang & Urgensi</span>
            <h2 class="td-section-h2">Mengapa KINERA Dibangun?</h2>
            <p class="td-section-sub">
                Tantangan perumusan kebijakan pembangunan kewilayahan sering terkendala oleh data yang terisolasi di masing-masing instansi. 
                KINERA hadir mewujudkan integrasi data yang akuntabel, terpusat, dan mudah dianalisis.
            </p>
        </div>

        <div class="td-pillar-grid">
            <div class="td-pillar-item">
                <div class="td-pillar-num">1</div>
                <h3 class="td-pillar-title">Eliminasi Silo Data</h3>
                <p class="td-pillar-desc">
                    Menyatukan data pembangunan dari 8 perangkat daerah teknis (energi, perhubungan, PU dan penataan ruang, lingkungan, pariwisata, perkim, kominfo, hingga perbatasan) dalam satu portal terpadu.
                </p>
            </div>
            <div class="td-pillar-item">
                <div class="td-pillar-num">2</div>
                <h3 class="td-pillar-title">Evidence-Based Policy</h3>
                <p class="td-pillar-desc">
                    Mendukung pimpinan BAPPEDA dan pembuat kebijakan dalam membandingkan capaian Realisasi terhadap Target Renstra/RKPD secara objektif berdasarkan bukti empiris.
                </p>
            </div>
            <div class="td-pillar-item">
                <div class="td-pillar-num">3</div>
                <h3 class="td-pillar-title">Efisiensi & Akurasi</h3>
                <p class="td-pillar-desc">
                    Menghilangkan rekapitulasi data manual yang memakan waktu dan berisiko salah hitung dengan otomasi perhitungan rata-rata, nilai tertinggi/terendah, dan multi-level aggregation.
                </p>
            </div>
            <div class="td-pillar-item">
                <div class="td-pillar-num">4</div>
                <h3 class="td-pillar-title">Interoperabilitas Sistem</h3>
                <p class="td-pillar-desc">
                    Dibangun dengan arsitektur RESTful API stateless yang siap diintegrasikan secara langsung ke aplikasi mobile (Flutter), dashboard eksekutif, atau sistem informasi nasional.
                </p>
            </div>
        </div>
    </section>

    <!-- ==================== FITUR-FITUR UNGGULAN ==================== -->
    <section id="fitur-unggulan" class="td-section-card">
        <div class="td-section-head">
            <span class="td-badge-pill">Kapabilitas Sistem</span>
            <h2 class="td-section-h2">Fitur Unggulan KINERA</h2>
            <p class="td-section-sub">
                Dirancang memenuhi standar penyajian data enterprise setara BPS dengan fleksibilitas analisis modern dan kecepatan akses tinggi.
            </p>
        </div>

        <div class="td-feature-grid">
            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Filter Multi-Dimensi Dinamis</h3>
                    <p>
                        Pilih indikator lintas sektor dengan pencarian realtime (debounce), fleksibilitas memilih data Realisasi atau Target, dan rentang tahun (2021 – 2026).
                    </p>
                </div>
            </div>

            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Header Bertingkat & Freeze Pane</h3>
                    <p>
                        Tabel berstruktur dua tingkat (*Grup Indikator & Tahun*) dengan kolom Kabupaten/Kota terkunci (*sticky*) untuk kemudahan membaca data lebar.
                    </p>
                </div>
            </div>

            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Visualisasi Grafik Interaktif</h3>
                    <p>
                        Beralih instan ke Grafik Batang (*komparasi spasial*) atau Grafik Garis (*tren capaian tahunan*) dilengkapi garis penanda Rata-rata Provinsi.
                    </p>
                </div>
            </div>

            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Ekspor Excel & CSV Standar</h3>
                    <p>
                        Unduh spreadsheet `.xlsx` dengan format angka numerik asli dan freeze panes, atau `.csv` dengan UTF-8 BOM dan pemisah titik koma resmi.
                    </p>
                </div>
            </div>

            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Kop Cetak & PDF Resmi BAPPEDA</h3>
                    <p>
                        Fitur cetak langsung yang dilengkapi Kop Surat resmi Pemerintah Provinsi Maluku Utara, tata letak otomatis rapi, dan siap dilaporkan ke pimpinan.
                    </p>
                </div>
            </div>

            <div class="td-feature-item">
                <div class="td-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="td-feature-content">
                    <h3>Impor Atomik & Audit Trail</h3>
                    <p>
                        Form unggah berkas Excel admin dengan transaksi basis data atomik (*jika ada 1 baris salah, dibatalkan*), validasi per baris, dan pencatatan log riwayat impor.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== OPD & SEKTOR IPW ==================== -->
    <section class="td-section-card">
        <div class="td-section-head">
            <span class="td-badge-pill">Cakupan Data</span>
            <h2 class="td-section-h2">{{ $totalOpd }} Organisasi Perangkat Daerah Tergabung</h2>
            <p class="td-section-sub">
                Data kinerja pembangunan dihimpun dari {{ $totalOpd }} dinas dan badan teknis di bawah koordinasi Bidang Infrastruktur dan Pengembangan Wilayah.
            </p>
        </div>

        <div class="td-opd-grid">
            @forelse($opdList as $opd)
                <div class="td-opd-card">
                    <div>
                        <h4 class="td-opd-title">{{ $opd->nama }}</h4>
                    </div>
                    <span class="td-opd-badge">
                        {{ $opd->indikator_count }} Indikator Terdaftar
                    </span>
                </div>
            @empty
                <p style="color: #64748b; text-align: center; grid-column: 1/-1;">Memuat daftar OPD...</p>
            @endforelse
        </div>
    </section>

    <!-- ==================== ALUR PENGGUNAAN (4 LANGKAH) ==================== -->
    <section class="td-section-card">
        <div class="td-section-head">
            <span class="td-badge-pill">Panduan Ringkas</span>
            <h2 class="td-section-h2">Alur Pengolahan Data dalam 4 Langkah</h2>
            <p class="td-section-sub">
                Membuat tabel komparasi dan visualisasi analitik sangat mudah dan dapat diselesaikan dalam hitungan detik.
            </p>
        </div>

        <div class="td-workflow-grid">
            <div class="td-workflow-step">
                <div class="td-workflow-num">1</div>
                <h4 class="td-workflow-title">Pilih Subjek & Indikator</h4>
                <p class="td-workflow-desc">
                    Tentukan sektor pembangunan dan pilih indikator kinerja yang ingin dievaluasi (atau gunakan fitur cari indikator).
                </p>
            </div>
            <div class="td-workflow-step">
                <div class="td-workflow-num">2</div>
                <h4 class="td-workflow-title">Tentukan Jenis & Tahun</h4>
                <p class="td-workflow-desc">
                    Pilih tipe data (Realisasi atau Target) serta rentang tahun yang tersedia (2021 – 2026), lalu klik Tambah ke Pilihan.
                </p>
            </div>
            <div class="td-workflow-step">
                <div class="td-workflow-num">3</div>
                <h4 class="td-workflow-title">Pilih Wilayah</h4>
                <p class="td-workflow-desc">
                    Pilih kabupaten/kota yang akan dibandingkan, atau centang Semua Wilayah untuk memantau 10 kabupaten/kota sekaligus.
                </p>
            </div>
            <div class="td-workflow-step">
                <div class="td-workflow-num">4</div>
                <h4 class="td-workflow-title">Sajikan & Ekspor</h4>
                <p class="td-workflow-desc">
                    Klik Tampilkan Tabel Dinamis. Nikmati tabel interaktif, telaah grafik analitik, dan unduh berkas Excel atau cetak PDF.
                </p>
            </div>
        </div>
    </section>

    <!-- ==================== CALL TO ACTION BANNER ==================== -->
    <section class="td-cta-banner">
        <h2 class="td-cta-title">Siap Mengeksplorasi Data Kinerja Maluku Utara?</h2>
        <p class="td-cta-desc">
            Rancang tabel dinamis Anda sekarang dan dapatkan wawasan analitik terukur untuk mendukung perencanaan pembangunan daerah yang berdaya saing.
        </p>
        <a href="{{ route('tabel-dinamis.index') }}" class="td-btn td-btn-primary td-btn-lg" style="background: #ffffff; color: #1d4ed8; border-color: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.25);">
            <span>Mulai Eksplorasi Data Sekarang</span>
            <span style="margin-left: 0.35rem;">→</span>
        </a>
    </section>

</div>
@endsection


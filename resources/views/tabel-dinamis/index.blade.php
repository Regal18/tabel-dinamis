@extends(config('tabel_dinamis.layout'))

@section('title', 'Tabel Dinamis Indikator Bidang IPW')

@push('styles')
<style>
    /* Styling mobile-first untuk Form Filter & Tabel Hasil */
    .td-banner-info {
        background-color: #e8f4fd;
        border-left: 4px solid #1976d2;
        color: #0d47a1;
        padding: 0.75rem 1rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .td-layout-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    @media (min-width: 992px) {
        .td-layout-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            align-items: start;
        }
    }
    .td-section-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .td-section-header {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.5rem;
    }
    .td-step-badge {
        background: #1e293b;
        color: #ffffff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .td-search-box {
        position: relative;
        margin-bottom: 0.5rem;
    }
    .td-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    .td-search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    .td-checkbox-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    .td-checkbox-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 0.35rem 0.6rem;
        border-radius: 4px;
        font-size: 0.825rem;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }
    .td-checkbox-pill:hover {
        background: #edf2f7;
        border-color: #94a3b8;
    }
    .td-checkbox-pill input[type="checkbox"] {
        margin: 0;
        cursor: pointer;
    }
    .td-selected-container {
        min-height: 80px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 6px;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .td-selected-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #3b82f6;
        padding: 0.6rem 0.75rem;
        border-radius: 4px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .td-selected-title {
        font-weight: 600;
        font-size: 0.875rem;
        color: #1e293b;
        line-height: 1.3;
    }
    .td-selected-meta {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.25rem;
    }
    .td-btn-delete {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 1.1rem;
        line-height: 1;
        padding: 0.2rem;
    }
    .td-btn-delete:hover {
        color: #b91c1c;
    }
    .td-action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .td-table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }
    .td-table-dynamic {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        min-width: 600px;
    }
    .td-table-dynamic th,
    .td-table-dynamic td {
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
    }
    .td-table-dynamic th {
        background-color: #1e293b;
        color: #ffffff;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
    }
    .td-table-dynamic th.td-th-wilayah {
        text-align: left;
        vertical-align: middle;
        min-width: 180px;
    }
    .td-table-dynamic td.td-align-right {
        text-align: right;
    }
    .td-table-dynamic td.td-align-center {
        text-align: center;
        color: #94a3b8;
    }
    .td-table-dynamic tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .td-table-dynamic tbody tr:hover {
        background-color: #f1f5f9;
    }
</style>
@endpush

@section('content')
<div id="td-alert-box"></div>

<!-- Banner Kewajiban Maksimal 2 Data -->
<div class="td-banner-info">
    <span>ℹ️</span>
    <span>Hanya dapat memilih maksimal 2 data</span>
</div>

<div class="td-card">
    <h2 style="font-size: 1.15rem; margin-bottom: 1rem;">Filter Tabel Dinamis</h2>

    <div class="td-layout-grid">
        <!-- Kolom 1: Pemilihan Subjek, Indikator, Jenis & Tahun -->
        <div>
            <div class="td-section-box">
                <div class="td-section-header">
                    <span class="td-step-badge">1</span>
                    <span>Pilih Indikator & Periode</span>
                </div>

                <!-- Dropdown Subjek -->
                <div class="td-form-group">
                    <label for="td-subjek">Subjek / Sektor Bidang IPW</label>
                    <select id="td-subjek">
                        <option value="">-- Memuat Subjek... --</option>
                    </select>
                </div>

                <!-- Pencarian & Dropdown Indikator (Debounce) -->
                <div class="td-form-group">
                    <label for="td-cari-indikator">Pencarian & Daftar Indikator</label>
                    <div class="td-search-box">
                        <input type="text" id="td-cari-indikator" class="td-search-input" placeholder="Ketik kata kunci untuk mencari indikator..." disabled>
                    </div>
                    <select id="td-indikator" disabled>
                        <option value="">-- Pilih Subjek Terlebih Dahulu --</option>
                    </select>
                    <small id="td-info-indikator" style="display: block; color: #64748b; margin-top: 0.35rem; font-size: 0.8rem;"></small>
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
                    <label>Tahun (Rentang 2021 - 2026)</label>
                    <div id="td-tahun-list" class="td-checkbox-wrap">
                        <span style="color: #94a3b8; font-size: 0.825rem;">Pilih indikator terlebih dahulu.</span>
                    </div>
                </div>

                <!-- Tombol Tambah -->
                <button type="button" id="td-btn-tambah" class="td-btn td-btn-primary" style="width: 100%;" disabled>
                    ➕ Tambah
                </button>
            </div>
        </div>

        <!-- Kolom 2: Data Terpilih & Wilayah & Aksi -->
        <div>
            <!-- Kontainer Data Terpilih (Maks 2) -->
            <div class="td-section-box">
                <div class="td-section-header">
                    <span class="td-step-badge">2</span>
                    <span>Data Terpilih</span>
                    <span id="td-data-counter" style="margin-left: auto; font-size: 0.8rem; color: #64748b; font-weight: normal;">(0 / 2)</span>
                </div>

                <div id="td-data-terpilih" class="td-selected-container">
                    <div id="td-terpilih-kosong" style="text-align: center; color: #94a3b8; font-size: 0.825rem; padding: 1rem 0;">
                        Belum ada data terpilih. Klik 'Tambah' pada panel sebelah kiri.
                    </div>
                </div>
            </div>

            <!-- Centang Wilayah (Judul Baris) -->
            <div class="td-section-box">
                <div class="td-section-header">
                    <span class="td-step-badge">3</span>
                    <span>Judul Baris (Wilayah)</span>
                </div>

                <div class="td-form-group">
                    <label class="td-checkbox-pill" style="font-weight: 600; margin-bottom: 0.5rem; display: inline-flex;">
                        <input type="checkbox" id="td-semua-wilayah" checked>
                        <span>Pilih Semua Wilayah</span>
                    </label>
                    <div id="td-wilayah-list" class="td-checkbox-wrap">
                        <span style="color: #94a3b8; font-size: 0.825rem;">Memuat daftar wilayah...</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi: Submit & Atur Ulang -->
            <div class="td-action-buttons">
                <button type="button" id="td-btn-submit" class="td-btn td-btn-secondary" style="flex: 2;" disabled>
                    🚀 Submit
                </button>
                <button type="button" id="td-btn-reset" class="td-btn td-btn-outline" style="flex: 1;">
                    🔄 Atur Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Kartu Tabel Hasil Dinamis Header Dua Tingkat -->
<div id="td-hasil-wrapper" class="td-card td-hidden">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
        <h2 style="font-size: 1.1rem; border: none; padding: 0; margin: 0;">Tabel Hasil Dinamis</h2>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" id="td-export-xlsx" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
                📥 Unduh Excel (.xlsx)
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

    // Helper Tampilkan Pesan Peringatan
    function tampilkanPesan(pesan, tipe = 'error') {
        dom.alertBox.innerHTML = `
            <div class="td-alert td-alert-${tipe}">
                ${pesan}
            </div>
        `;
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
        dom.subjek.innerHTML = '<option value="">-- Pilih Subjek / Sektor --</option>';
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
        dom.indikator.innerHTML = '<option value="">-- Memuat Indikator... --</option>';
        dom.indikator.disabled = true;
        dom.jenis.disabled = true;
        dom.btnTambah.disabled = true;
        dom.infoIndikator.textContent = '';
        dom.tahunList.innerHTML = '<span style="color: #94a3b8; font-size: 0.825rem;">Pilih indikator terlebih dahulu.</span>';

        if (!subjekId) {
            dom.indikator.innerHTML = '<option value="">-- Pilih Subjek Terlebih Dahulu --</option>';
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

            dom.indikator.innerHTML = '<option value="">-- Pilih Indikator --</option>';
            if (indikators.length === 0) {
                dom.indikator.innerHTML = '<option value="">-- Tidak ada indikator yang cocok --</option>';
            } else {
                indikators.forEach(ind => {
                    const opt = document.createElement('option');
                    opt.value = ind.id;
                    opt.textContent = `${ind.kode} - ${ind.label}`;
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
            dom.tahunList.innerHTML = '<span style="color: #94a3b8; font-size: 0.825rem;">Pilih indikator terlebih dahulu.</span>';
            return;
        }

        dom.jenis.disabled = false;
        const terpilih = state.indikator.find(i => i.id == indId);
        if (terpilih) {
            const meta = [];
            if (terpilih.satuan) meta.push(`Satuan: <strong>${terpilih.satuan}</strong>`);
            if (terpilih.opd) meta.push(`OPD: <strong>${terpilih.opd}</strong>`);
            dom.infoIndikator.innerHTML = meta.join(' | ');
        }

        dom.tahunList.innerHTML = '<span style="color: #94a3b8; font-size: 0.825rem;">Memeriksa ketersediaan tahun...</span>';

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
            label.innerHTML = `
                <input type="checkbox" class="td-cb-thn" value="${th}" ${adaData ? 'checked' : ''}>
                <span>${th} ${adaData ? '✓' : ''}</span>
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
                    <div class="td-selected-meta">Tahun: <strong>${item.tahun.join(', ')}</strong> ${item.satuan ? `(${item.satuan})` : ''}</div>
                </div>
                <button type="button" class="td-btn-delete" title="Hapus Data" data-index="${index}">✖</button>
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
            dom.btnSubmit.innerHTML = '🚀 Submit';
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
            thTingkat1 += `<th colspan="${colSpan}">[${g.jenis}] ${g.judul}${satuanTeks}</th>`;

            g.tahun.forEach(th => {
                thTingkat2 += `<th style="min-width: 80px;">${th}</th>`;
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
            bodyHtml = `<tr><td colspan="100" class="td-align-center" style="padding: 1.5rem;">Tidak ada data ditemukan.</td></tr>`;
        } else {
            baris.forEach(b => {
                bodyHtml += `<tr><td style="font-weight: 600;">${b.wilayah}</td>`;
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
        } catch (err) {
            tampilkanPesan(err.message, 'error');
        }
    }

    dom.exportXlsx.addEventListener('click', () => unduhBerkasExport('xlsx'));
    dom.exportCsv.addEventListener('click', () => unduhBerkasExport('csv'));

    // Jalankan
    inisialisasi();
})();
</script>
@endpush

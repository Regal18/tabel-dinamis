@extends($layout)

@section('title', 'Tabel Dinamis Indikator Bidang IPW')

@push('styles')
<style>
    /* Styling khusus form filter & tabel dinamis */
    .td-grid-2 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .td-grid-2 {
            grid-template-columns: 1fr 1fr;
        }
    }
    .td-step-box {
        background: #fafbfc;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .td-step-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #16213e;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .td-step-num {
        background: #16213e;
        color: #fff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }
    .td-checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .td-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #fff;
        border: 1px solid #ced4da;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        user-select: none;
    }
    .td-checkbox-label:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
    }
    .td-checkbox-label input[type="checkbox"] {
        margin: 0;
        cursor: pointer;
    }
    .td-selected-box {
        min-height: 56px;
        background: #fff;
        border: 1px dashed #ced4da;
        border-radius: 6px;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .td-selected-item {
        background: #eef2f7;
        border-left: 3px solid #16213e;
        padding: 0.5rem 0.75rem;
        border-radius: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }
    .td-selected-info {
        font-size: 0.85rem;
    }
    .td-selected-info strong {
        display: block;
        color: #16213e;
    }
    .td-selected-meta {
        font-size: 0.75rem;
        color: #555;
    }
    .td-counter-badge {
        font-size: 0.75rem;
        font-weight: normal;
        color: #666;
        margin-left: auto;
    }
    .td-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .td-export-group {
        display: flex;
        gap: 0.5rem;
    }
    .td-th-center {
        text-align: center !important;
    }
    .td-td-right {
        text-align: right !important;
    }
</style>
@endpush

@section('content')
<div id="td-alert-container"></div>

<!-- Kartu Filter Dinamis (Meniru BPS Malut) -->
<div class="td-card">
    <h2>Filter Tabel Dinamis</h2>
    
    <div class="td-grid-2">
        <!-- Kolom Kiri: Pilihan Indikator & Tambah Data -->
        <div>
            <div class="td-step-box">
                <div class="td-step-title">
                    <span class="td-step-num">1</span>
                    <span>Pilih Indikator & Periode</span>
                </div>

                <div class="td-form-group">
                    <label for="td-select-subjek">Subjek / Sektor</label>
                    <select id="td-select-subjek">
                        <option value="">-- Memuat Subjek... --</option>
                    </select>
                </div>

                <div class="td-form-group">
                    <label for="td-select-indikator">Tabel / Indikator</label>
                    <select id="td-select-indikator" disabled>
                        <option value="">-- Pilih Subjek Terlebih Dahulu --</option>
                    </select>
                    <small id="td-indikator-info" class="td-mt-1" style="display:block; color:#666;"></small>
                </div>

                <div class="td-form-group">
                    <label for="td-select-jenis">Jenis Data</label>
                    <select id="td-select-jenis" disabled>
                        <option value="Realisasi">Realisasi</option>
                        <option value="Target">Target</option>
                    </select>
                </div>

                <div class="td-form-group">
                    <label>Tahun (Rentang 2021 - 2026)</label>
                    <div id="td-tahun-container" class="td-checkbox-group">
                        <span style="color:#888; font-size:0.85rem;">Pilih indikator untuk melihat ketersediaan tahun.</span>
                    </div>
                </div>

                <button type="button" id="td-btn-tambah" class="td-btn td-btn-primary" style="width: 100%;" disabled>
                    ➕ Tambah ke Data Terpilih
                </button>
            </div>
        </div>

        <!-- Kolom Kanan: Data Terpilih & Filter Wilayah -->
        <div>
            <!-- Box Data Terpilih (Maks 2) -->
            <div class="td-step-box">
                <div class="td-step-title">
                    <span class="td-step-num">2</span>
                    <span>Data Terpilih</span>
                    <span id="td-counter-data" class="td-counter-badge">(0 / 2)</span>
                </div>

                <div id="td-selected-box" class="td-selected-box">
                    <div id="td-selected-empty" style="text-align: center; color: #888; font-size: 0.85rem; padding: 0.5rem 0;">
                        Belum ada data yang ditambahkan (Maksimal 2).
                    </div>
                </div>
            </div>

            <!-- Box Judul Baris / Wilayah -->
            <div class="td-step-box">
                <div class="td-step-title">
                    <span class="td-step-num">3</span>
                    <span>Judul Baris / Wilayah</span>
                </div>

                <div class="td-form-group">
                    <label class="td-checkbox-label" style="font-weight: 600; margin-bottom: 0.5rem;">
                        <input type="checkbox" id="td-wilayah-semua" checked>
                        <span>Pilih Semua Wilayah</span>
                    </label>
                    <div id="td-wilayah-container" class="td-checkbox-group">
                        <span style="color:#888; font-size:0.85rem;">Memuat daftar wilayah...</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Submit -->
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <button type="button" id="td-btn-submit" class="td-btn td-btn-secondary" style="flex: 2;" disabled>
                    🚀 Tampilkan Tabel
                </button>
                <button type="button" id="td-btn-reset" class="td-btn td-btn-outline" style="flex: 1;">
                    Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Kartu Tabel Hasil Dinamis -->
<div id="td-card-hasil" class="td-card td-hidden">
    <div class="td-header-actions">
        <h2 style="margin-bottom: 0; border: none; padding: 0;">Tabel Hasil Dinamis</h2>
        <div class="td-export-group">
            <button type="button" id="td-btn-export-xlsx" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
                📥 Unduh Excel (.xlsx)
            </button>
            <button type="button" id="td-btn-export-csv" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
                📄 Unduh CSV
            </button>
        </div>
    </div>

    <div class="td-table-wrap">
        <table id="td-tabel-hasil" class="td-table">
            <thead id="td-tabel-thead"></thead>
            <tbody id="td-tabel-tbody"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // Konfigurasi & Base URL
    const BASE_URL = '{{ url(config("tabel_dinamis.prefix", "tabel-dinamis")) }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const MAKS_DATA = {{ config('tabel_dinamis.maks_data_per_tabel', 2) }};
    const TAHUN_MIN = {{ config('tabel_dinamis.tahun_min', 2021) }};
    const TAHUN_MAX = {{ config('tabel_dinamis.tahun_max', 2026) }};

    // State Aplikasi
    let state = {
        subjekList: [],
        indikatorList: [],
        wilayahList: [],
        selectedData: [], // Array item: { indikator_id, label, satuan, opd, jenis, tahun: [] }
        lastPayload: null // Payload terakhir yang di-submit untuk export
    };

    // Elemen DOM
    const el = {
        alertContainer: document.getElementById('td-alert-container'),
        selectSubjek: document.getElementById('td-select-subjek'),
        selectIndikator: document.getElementById('td-select-indikator'),
        selectJenis: document.getElementById('td-select-jenis'),
        indikatorInfo: document.getElementById('td-indikator-info'),
        tahunContainer: document.getElementById('td-tahun-container'),
        btnTambah: document.getElementById('td-btn-tambah'),
        selectedBox: document.getElementById('td-selected-box'),
        selectedEmpty: document.getElementById('td-selected-empty'),
        counterData: document.getElementById('td-counter-data'),
        wilayahSemua: document.getElementById('td-wilayah-semua'),
        wilayahContainer: document.getElementById('td-wilayah-container'),
        btnSubmit: document.getElementById('td-btn-submit'),
        btnReset: document.getElementById('td-btn-reset'),
        cardHasil: document.getElementById('td-card-hasil'),
        tabelThead: document.getElementById('td-tabel-thead'),
        tabelTbody: document.getElementById('td-tabel-tbody'),
        btnExportXlsx: document.getElementById('td-btn-export-xlsx'),
        btnExportCsv: document.getElementById('td-btn-export-csv')
    };

    // Tampilkan Notifikasi Pesan / Alert
    function showAlert(message, type = 'danger') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `td-alert td-alert-${type === 'danger' ? 'error' : type}`;
        alertDiv.textContent = message;
        el.alertContainer.innerHTML = '';
        el.alertContainer.appendChild(alertDiv);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function clearAlert() {
        el.alertContainer.innerHTML = '';
    }

    // Helper Fetch API
    async function apiRequest(endpoint, options = {}) {
        const defaultHeaders = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        };
        options.headers = { ...defaultHeaders, ...options.headers };

        const response = await fetch(`${BASE_URL}/api${endpoint}`, options);
        if (!response.ok) {
            let errorMsg = 'Terjadi kesalahan sistem.';
            try {
                const errData = await response.json();
                if (errData.message) errorMsg = errData.message;
                if (errData.errors) {
                    const firstError = Object.values(errData.errors)[0];
                    if (Array.isArray(firstError)) errorMsg = firstError[0];
                }
            } catch (e) {}
            throw new Error(errorMsg);
        }
        return response.json();
    }

    // 1. Inisialisasi: Muat Subjek & Wilayah
    async function init() {
        try {
            const [subjekRes, wilayahRes] = await Promise.all([
                apiRequest('/subjek'),
                apiRequest('/wilayah')
            ]);

            state.subjekList = subjekRes;
            state.wilayahList = wilayahRes;

            renderSubjekOptions();
            renderWilayahCheckboxes();
        } catch (err) {
            showAlert(`Gagal memuat data awal: ${err.message}`);
        }
    }

    function renderSubjekOptions() {
        el.selectSubjek.innerHTML = '<option value="">-- Pilih Subjek --</option>';
        state.subjekList.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama;
            el.selectSubjek.appendChild(opt);
        });
    }

    function renderWilayahCheckboxes() {
        el.wilayahContainer.innerHTML = '';
        state.wilayahList.forEach(w => {
            const label = document.createElement('label');
            label.className = 'td-checkbox-label';
            label.innerHTML = `
                <input type="checkbox" class="td-cb-wilayah" value="${w.id}" checked>
                <span>${w.nama}</span>
            `;
            el.wilayahContainer.appendChild(label);
        });

        // Event listener centang wilayah
        document.querySelectorAll('.td-cb-wilayah').forEach(cb => {
            cb.addEventListener('change', updateWilayahSemuaState);
        });
    }

    function updateWilayahSemuaState() {
        const all = document.querySelectorAll('.td-cb-wilayah');
        const checked = document.querySelectorAll('.td-cb-wilayah:checked');
        el.wilayahSemua.checked = (all.length > 0 && all.length === checked.length);
        el.wilayahSemua.indeterminate = (checked.length > 0 && checked.length < all.length);
    }

    el.wilayahSemua.addEventListener('change', function() {
        const checkStatus = this.checked;
        document.querySelectorAll('.td-cb-wilayah').forEach(cb => {
            cb.checked = checkStatus;
        });
    });

    // 2. Event: Subjek Berubah -> Muat Indikator
    el.selectSubjek.addEventListener('change', async function() {
        const subjekId = this.value;
        el.selectIndikator.innerHTML = '<option value="">-- Memuat Indikator... --</option>';
        el.selectIndikator.disabled = true;
        el.selectJenis.disabled = true;
        el.btnTambah.disabled = true;
        el.indikatorInfo.textContent = '';
        el.tahunContainer.innerHTML = '<span style="color:#888; font-size:0.85rem;">Pilih indikator terlebih dahulu.</span>';

        if (!subjekId) {
            el.selectIndikator.innerHTML = '<option value="">-- Pilih Subjek Terlebih Dahulu --</option>';
            return;
        }

        try {
            const indikators = await apiRequest(`/indikator?subjek_id=${subjekId}`);
            state.indikatorList = indikators;

            el.selectIndikator.innerHTML = '<option value="">-- Pilih Indikator --</option>';
            indikators.forEach(ind => {
                const opt = document.createElement('option');
                opt.value = ind.id;
                opt.textContent = `${ind.kode} - ${ind.label}`;
                el.selectIndikator.appendChild(opt);
            });
            el.selectIndikator.disabled = false;
        } catch (err) {
            showAlert(`Gagal memuat indikator: ${err.message}`);
        }
    });

    // 3. Event: Indikator atau Jenis Berubah -> Muat Tahun yang Tersedia
    el.selectIndikator.addEventListener('change', handleIndikatorChange);
    el.selectJenis.addEventListener('change', handleIndikatorChange);

    async function handleIndikatorChange() {
        const indId = el.selectIndikator.value;
        const jenis = el.selectJenis.value;

        if (!indId) {
            el.selectJenis.disabled = true;
            el.btnTambah.disabled = true;
            el.indikatorInfo.textContent = '';
            el.tahunContainer.innerHTML = '<span style="color:#888; font-size:0.85rem;">Pilih indikator terlebih dahulu.</span>';
            return;
        }

        el.selectJenis.disabled = false;
        const selectedInd = state.indikatorList.find(i => i.id == indId);
        if (selectedInd) {
            let info = [];
            if (selectedInd.satuan) info.push(`Satuan: <strong>${selectedInd.satuan}</strong>`);
            if (selectedInd.opd) info.push(`OPD: <strong>${selectedInd.opd}</strong>`);
            el.indikatorInfo.innerHTML = info.join(' | ');
        }

        el.tahunContainer.innerHTML = '<span style="color:#888; font-size:0.85rem;">Memeriksa tahun data...</span>';

        try {
            const tahunTersedia = await apiRequest(`/tahun?indikator_id=${indId}&jenis=${jenis}`);
            renderTahunCheckboxes(tahunTersedia);
            checkBtnTambahState();
        } catch (err) {
            renderTahunCheckboxes([]);
            checkBtnTambahState();
        }
    }

    function renderTahunCheckboxes(tahunTersedia) {
        el.tahunContainer.innerHTML = '';
        for (let th = TAHUN_MIN; th <= TAHUN_MAX; th++) {
            const adaData = tahunTersedia.includes(th);
            const label = document.createElement('label');
            label.className = 'td-checkbox-label';
            label.innerHTML = `
                <input type="checkbox" class="td-cb-tahun" value="${th}" ${adaData ? 'checked' : ''}>
                <span>${th} ${adaData ? '✓' : ''}</span>
            `;
            el.tahunContainer.appendChild(label);
        }

        document.querySelectorAll('.td-cb-tahun').forEach(cb => {
            cb.addEventListener('change', checkBtnTambahState);
        });
    }

    function checkBtnTambahState() {
        const indId = el.selectIndikator.value;
        const tahunChecked = document.querySelectorAll('.td-cb-tahun:checked').length;
        const reachLimit = state.selectedData.length >= MAKS_DATA;
        el.btnTambah.disabled = (!indId || tahunChecked === 0 || reachLimit);
    }

    // 4. Tambah ke Data Terpilih
    el.btnTambah.addEventListener('click', function() {
        clearAlert();
        if (state.selectedData.length >= MAKS_DATA) {
            showAlert(`Maksimal hanya dapat memilih ${MAKS_DATA} indikator/data.`, 'warning');
            return;
        }

        const indId = parseInt(el.selectIndikator.value, 10);
        const selectedInd = state.indikatorList.find(i => i.id == indId);
        const jenis = el.selectJenis.value;
        const tahunChecked = Array.from(document.querySelectorAll('.td-cb-tahun:checked'))
            .map(cb => parseInt(cb.value, 10))
            .sort((a, b) => a - b);

        if (!selectedInd || tahunChecked.length === 0) {
            showAlert('Pilih minimal satu tahun.', 'warning');
            return;
        }

        // Cek duplikasi indikator + jenis
        const duplicate = state.selectedData.find(item => item.indikator_id === indId && item.jenis === jenis);
        if (duplicate) {
            showAlert(`Kombinasi indikator "${selectedInd.label}" dengan jenis "${jenis}" sudah ada di data terpilih.`, 'warning');
            return;
        }

        state.selectedData.push({
            indikator_id: indId,
            kode: selectedInd.kode,
            label: selectedInd.label,
            satuan: selectedInd.satuan || null,
            opd: selectedInd.opd || null,
            jenis: jenis,
            tahun: tahunChecked
        });

        renderSelectedData();
        checkBtnTambahState();
    });

    function renderSelectedData() {
        el.selectedBox.innerHTML = '';
        el.counterData.textContent = `(${state.selectedData.length} / ${MAKS_DATA})`;

        if (state.selectedData.length === 0) {
            el.selectedBox.appendChild(el.selectedEmpty);
            el.btnSubmit.disabled = true;
            return;
        }

        el.btnSubmit.disabled = false;

        state.selectedData.forEach((item, idx) => {
            const div = document.createElement('div');
            div.className = 'td-selected-item';
            div.innerHTML = `
                <div class="td-selected-info">
                    <strong>[${item.jenis}] ${item.label}</strong>
                    <div class="td-selected-meta">Tahun: ${item.tahun.join(', ')} ${item.satuan ? `(${item.satuan})` : ''}</div>
                </div>
                <button type="button" class="td-chip-hapus" title="Hapus" data-index="${idx}" style="background:none; border:none;">✖</button>
            `;
            el.selectedBox.appendChild(div);
        });

        document.querySelectorAll('.td-chip-hapus').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                state.selectedData.splice(idx, 1);
                renderSelectedData();
                checkBtnTambahState();
            });
        });
    }

    // 5. Submit Tampilkan Tabel
    el.btnSubmit.addEventListener('click', async function() {
        clearAlert();

        if (state.selectedData.length === 0) {
            showAlert('Pilih minimal 1 data indikator terlebih dahulu.', 'warning');
            return;
        }

        // Kumpulkan Wilayah
        const cbWilayahChecked = Array.from(document.querySelectorAll('.td-cb-wilayah:checked'));
        if (cbWilayahChecked.length === 0) {
            showAlert('Pilih minimal 1 wilayah.', 'warning');
            return;
        }

        const wilayahIds = el.wilayahSemua.checked && cbWilayahChecked.length === state.wilayahList.length
            ? null 
            : cbWilayahChecked.map(cb => parseInt(cb.value, 10));

        const payload = {
            wilayah_ids: wilayahIds,
            data: state.selectedData.map(item => ({
                indikator_id: item.indikator_id,
                jenis: item.jenis,
                tahun: item.tahun
            }))
        };

        el.btnSubmit.disabled = true;
        el.btnSubmit.innerHTML = '<span class="td-spinner"></span> Memuat Data...';

        try {
            const tabelData = await apiRequest('/tabel', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            state.lastPayload = payload;
            renderTabelHasil(tabelData);
            el.cardHasil.classList.remove('td-hidden');
            el.cardHasil.scrollIntoView({ behavior: 'smooth' });
        } catch (err) {
            showAlert(err.message, 'danger');
        } finally {
            el.btnSubmit.disabled = false;
            el.btnSubmit.innerHTML = '🚀 Tampilkan Tabel';
        }
    });

    // 6. Render Tabel Hasil Dinamis
    function renderTabelHasil(tabelData) {
        const { grup, baris } = tabelData;

        // Susun Header
        let thRow1 = '<tr><th rowspan="2" style="vertical-align: middle;">Kabupaten / Kota</th>';
        let thRow2 = '<tr>';

        grup.forEach(g => {
            const colSpan = g.tahun.length;
            const satuanLabel = g.satuan ? ` (${g.satuan})` : '';
            thRow1 += `<th colspan="${colSpan}" class="td-th-center">[${g.jenis}] ${g.judul}${satuanLabel}</th>`;
            g.tahun.forEach(th => {
                thRow2 += `<th class="td-th-center" style="min-width: 90px;">${th}</th>`;
            });
        });

        thRow1 += '</tr>';
        thRow2 += '</tr>';
        el.tabelThead.innerHTML = thRow1 + thRow2;

        // Susun Baris
        let tbodyHtml = '';
        if (baris.length === 0) {
            tbodyHtml = `<tr><td colspan="100" style="text-align:center; padding:1.5rem; color:#888;">Tidak ada data ditemukan.</td></tr>`;
        } else {
            baris.forEach(b => {
                tbodyHtml += `<tr><td style="font-weight: 600;">${b.wilayah}</td>`;
                b.nilai.forEach(grupNilai => {
                    grupNilai.forEach(val => {
                        if (val === null || val === undefined) {
                            tbodyHtml += `<td class="td-nilai-kosong td-td-right">-</td>`;
                        } else {
                            // Format angka dengan pemisah desimal
                            const formatted = Number(val).toLocaleString('id-ID', { maximumFractionDigits: 4 });
                            tbodyHtml += `<td class="td-td-right">${formatted}</td>`;
                        }
                    });
                });
                tbodyHtml += '</tr>';
            });
        }
        el.tabelTbody.innerHTML = tbodyHtml;
    }

    // 7. Reset Form
    el.btnReset.addEventListener('click', function() {
        state.selectedData = [];
        renderSelectedData();
        checkBtnTambahState();
        el.cardHasil.classList.add('td-hidden');
        clearAlert();
    });

    // 8. Export Handler (xlsx / csv)
    function handleExport(format) {
        if (!state.lastPayload) {
            showAlert('Silakan tampilkan tabel terlebih dahulu sebelum mengunduh.', 'warning');
            return;
        }

        // Form submit dinamis untuk download file biner
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${BASE_URL}/api/export?format=${format}`;
        form.target = '_blank';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = CSRF_TOKEN;
        form.appendChild(csrfInput);

        // Jika endpoint /api/export menerima JSON body via AJAX Blob
        downloadBlobExport(format);
    }

    async function downloadBlobExport(format) {
        try {
            const response = await fetch(`${BASE_URL}/api/export?format=${format}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/octet-stream, application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(state.lastPayload)
            });

            if (!response.ok) {
                let errText = 'Gagal mengunduh file export.';
                try {
                    const errJson = await response.json();
                    if (errJson.message) errText = errJson.message;
                } catch(e) {}
                throw new Error(errText);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `tabel-dinamis-${new Date().toISOString().slice(0,10)}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        } catch (err) {
            showAlert(err.message, 'danger');
        }
    }

    el.btnExportXlsx.addEventListener('click', () => handleExport('xlsx'));
    el.btnExportCsv.addEventListener('click', () => handleExport('csv'));

    // Jalankan init
    init();
})();
</script>
@endpush

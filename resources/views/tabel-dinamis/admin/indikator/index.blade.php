@extends($layout)

@section('title', 'Master Data Indikator — Admin KINERA')

@push('styles')
<style>
    .td-admin-nav-tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid var(--gray-200);
        margin-bottom: 1.25rem;
    }
    .td-admin-nav-tab {
        padding: 0.6rem 1.1rem;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--gray-600);
        text-decoration: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .td-admin-nav-tab:hover {
        color: var(--deep-blue-700);
    }
    .td-admin-nav-tab.active {
        color: var(--deep-blue-700);
        border-bottom-color: var(--deep-blue-600);
    }

    .td-admin-card {
        background: var(--white);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04),
                    0 6px 16px -4px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }
    .td-admin-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #1d4ed8;
    }

    .td-toolbar-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    .td-filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .td-input-filter {
        padding: 0.45rem 0.75rem;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        color: var(--gray-800);
        background: var(--white);
    }
    .td-input-filter:focus {
        outline: none;
        border-color: var(--deep-blue-600);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .td-table-indikator {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .td-table-indikator th {
        background: var(--deep-blue-800);
        color: var(--white);
        padding: 0.7rem 0.85rem;
        font-weight: 600;
        text-align: left;
        border: 1px solid var(--deep-blue-900);
    }
    .td-table-indikator td {
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .td-table-indikator tbody tr:nth-child(even) {
        background-color: var(--gray-50);
    }
    .td-table-indikator tbody tr:hover {
        background-color: var(--deep-blue-50);
    }

    .td-badge-code {
        font-family: monospace;
        font-size: 0.78rem;
        font-weight: 700;
        background: var(--deep-blue-50);
        color: var(--deep-blue-700);
        border: 1px solid var(--deep-blue-200);
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        white-space: nowrap;
    }

    /* Modal Form Styling */
    .td-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(10, 22, 40, 0.65);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
    }
    .td-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .td-modal-box {
        background: var(--white);
        border-radius: var(--radius-lg);
        max-width: 580px;
        width: 100%;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        transform: translateY(-20px);
        transition: transform 0.25s ease;
        overflow: hidden;
    }
    .td-modal-overlay.active .td-modal-box {
        transform: translateY(0);
    }
    .td-modal-header {
        background: linear-gradient(135deg, var(--deep-blue-900) 0%, var(--deep-blue-800) 100%);
        color: var(--white);
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .td-modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
    }
    .td-modal-close {
        background: none;
        border: none;
        color: var(--white);
        font-size: 1.3rem;
        cursor: pointer;
        opacity: 0.75;
        transition: opacity 0.2s;
    }
    .td-modal-close:hover {
        opacity: 1;
    }
    .td-modal-body {
        padding: 1.25rem;
        max-height: 75vh;
        overflow-y: auto;
    }
    .td-modal-footer {
        padding: 0.85rem 1.25rem;
        background: var(--gray-50);
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .td-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
    }
    .td-form-col-2 {
        grid-column: span 2;
    }
</style>
@endpush

@section('content')
<!-- Nav Tabs Admin -->
<div class="td-admin-nav-tabs">
    <a href="{{ route('tabel-dinamis.admin.impor.form') }}" class="td-admin-nav-tab">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        <span>Impor Berkas Excel</span>
    </a>
    <a href="{{ route('tabel-dinamis.admin.indikator.index') }}" class="td-admin-nav-tab active">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        <span>Master Data Indikator</span>
    </a>
</div>

@if(session('success'))
<div class="td-alert td-alert-success" style="margin-bottom: 1rem;">
    <span>✓ {{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="td-alert td-alert-error" style="margin-bottom: 1rem;">
    <ul style="margin-left: 1.2rem;">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="td-admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 2px solid var(--deep-blue-100); padding-bottom: 0.6rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--deep-blue-800); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span class="td-h2-icon" style="width:28px;height:28px;background:var(--deep-blue-50);border-radius:var(--radius-sm);display:inline-flex;align-items:center;justify-content:center;color:var(--deep-blue-700);">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </span>
            <span>Master Data Indikator Pembangunan Daerah IPW</span>
        </h2>
        <button type="button" class="td-btn td-btn-primary" id="td-btn-buka-tambah" style="font-size: 0.82rem;">
            + Tambah Indikator Baru
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <form action="{{ route('tabel-dinamis.admin.indikator.index') }}" method="GET" class="td-toolbar-filter">
        <div class="td-filter-group">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode atau nama..." class="td-input-filter" style="width: 200px;">
            <select name="subjek_id" class="td-input-filter">
                <option value="">— Semua Subjek —</option>
                @foreach($subjekList as $sub)
                    <option value="{{ $sub->id }}" {{ request('subjek_id') == $sub->id ? 'selected' : '' }}>
                        {{ $sub->nama }}
                    </option>
                @endforeach
            </select>
            <select name="opd_id" class="td-input-filter">
                <option value="">— Semua OPD —</option>
                @foreach($opdList as $opd)
                    <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                        {{ $opd->nama }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="td-btn td-btn-outline" style="font-size: 0.8rem; padding: 0.42rem 0.85rem;">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['q', 'subjek_id', 'opd_id']))
                <a href="{{ route('tabel-dinamis.admin.indikator.index') }}" class="td-btn td-btn-outline" style="font-size: 0.8rem; padding: 0.42rem 0.75rem; color: var(--danger); border-color: var(--danger-border);">
                    ✕ Reset
                </a>
            @endif
        </div>
        <div style="font-size: 0.8rem; color: var(--gray-500);">
            Total: <strong>{{ $indikatorList->total() }}</strong> indikator
        </div>
    </form>

    <!-- Tabel Daftar Indikator -->
    <div style="overflow-x: auto;">
        <table class="td-table-indikator">
            <thead>
                <tr>
                    <th style="width: 110px;">Kode</th>
                    <th>Nama Indikator</th>
                    <th>Sub Indikator</th>
                    <th style="width: 140px;">Subjek / Sektor</th>
                    <th style="width: 150px;">Perangkat Daerah (OPD)</th>
                    <th style="width: 90px; text-align: center;">Satuan</th>
                    <th style="width: 80px; text-align: center;">Data</th>
                    <th style="width: 110px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($indikatorList as $ind)
                <tr>
                    <td><span class="td-badge-code">{{ $ind->kode }}</span></td>
                    <td style="font-weight: 600; color: var(--deep-blue-800);">{{ $ind->nama }}</td>
                    <td style="color: var(--gray-600);">{{ $ind->sub_indikator ?: '-' }}</td>
                    <td><span style="font-size: 0.78rem; font-weight: 500;">{{ $ind->subjek?->nama ?: '-' }}</span></td>
                    <td><span style="font-size: 0.78rem; color: var(--gray-600);">{{ $ind->opd?->nama ?: '-' }}</span></td>
                    <td style="text-align: center;"><span style="font-size: 0.78rem; background: #f0fdf4; color: #166534; padding: 0.15rem 0.4rem; border-radius: 4px; border: 1px solid #bbf7d0;">{{ $ind->satuan ?: '-' }}</span></td>
                    <td style="text-align: center;">
                        <span style="font-size: 0.78rem; font-weight: 700; color: {{ $ind->data_indikator_count > 0 ? '#1d4ed8' : '#94a3b8' }};">
                            {{ $ind->data_indikator_count }}
                        </span>
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <button type="button" 
                                class="td-btn td-btn-outline td-btn-edit" 
                                style="font-size: 0.75rem; padding: 0.2rem 0.5rem;"
                                data-id="{{ $ind->id }}"
                                data-kode="{{ $ind->kode }}"
                                data-nama="{{ $ind->nama }}"
                                data-sub_indikator="{{ $ind->sub_indikator }}"
                                data-subjek_id="{{ $ind->subjek_id }}"
                                data-opd_id="{{ $ind->opd_id }}"
                                data-satuan="{{ $ind->satuan }}"
                                data-keterangan="{{ $ind->keterangan }}">
                            ✏️ Edit
                        </button>
                        <form action="{{ route('tabel-dinamis.admin.indikator.destroy', $ind->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator [{{ $ind->kode }}]? Seluruh data terkait indikator ini akan ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="td-btn td-btn-outline" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; color: var(--danger); border-color: var(--danger-border);" title="Hapus Indikator">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--gray-400);">
                        Tidak ada data indikator yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.25rem;">
        {{ $indikatorList->links() }}
    </div>
</div>

<!-- Modal Tambah / Edit Indikator -->
<div class="td-modal-overlay" id="td-modal-indikator">
    <div class="td-modal-box">
        <div class="td-modal-header">
            <h3 class="td-modal-title" id="td-modal-title">Tambah Indikator Baru</h3>
            <button type="button" class="td-modal-close" id="td-modal-close">✕</button>
        </div>
        <form id="td-form-indikator" method="POST" action="{{ route('tabel-dinamis.admin.indikator.store') }}">
            @csrf
            <div id="td-method-field"></div>
            <div class="td-modal-body">
                <div class="td-form-grid">
                    <div>
                        <label class="td-label" for="inp-kode">Kode Indikator <span style="color:red">*</span></label>
                        <input type="text" id="inp-kode" name="kode" class="td-input-filter" style="width:100%;" required placeholder="Contoh: PUPR-01">
                    </div>
                    <div>
                        <label class="td-label" for="inp-satuan">Satuan</label>
                        <input type="text" id="inp-satuan" name="satuan" class="td-input-filter" style="width:100%;" placeholder="Contoh: %, Km, Unit">
                    </div>
                    <div>
                        <label class="td-label" for="inp-subjek">Subjek / Sektor <span style="color:red">*</span></label>
                        <select id="inp-subjek" name="subjek_id" class="td-input-filter" style="width:100%;" required>
                            <option value="">— Pilih Subjek —</option>
                            @foreach($subjekList as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="td-label" for="inp-opd">Perangkat Daerah (OPD) <span style="color:red">*</span></label>
                        <select id="inp-opd" name="opd_id" class="td-input-filter" style="width:100%;" required>
                            <option value="">— Pilih OPD —</option>
                            @foreach($opdList as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="td-form-col-2">
                        <label class="td-label" for="inp-nama">Nama Indikator <span style="color:red">*</span></label>
                        <input type="text" id="inp-nama" name="nama" class="td-input-filter" style="width:100%;" required placeholder="Nama lengkap indikator...">
                    </div>
                    <div class="td-form-col-2">
                        <label class="td-label" for="inp-sub">Sub Indikator (Opsional)</label>
                        <input type="text" id="inp-sub" name="sub_indikator" class="td-input-filter" style="width:100%;" placeholder="Keterangan rincian/sub jika ada...">
                    </div>
                    <div class="td-form-col-2">
                        <label class="td-label" for="inp-keterangan">Keterangan / Definisi Operasional</label>
                        <textarea id="inp-keterangan" name="keterangan" class="td-input-filter" style="width:100%; height: 60px;" placeholder="Definisi operasional atau catatan tambahan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="td-modal-footer">
                <button type="button" class="td-btn td-btn-outline" id="td-modal-cancel">Batal</button>
                <button type="submit" class="td-btn td-btn-primary" id="td-btn-save">Simpan Indikator</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('td-modal-indikator');
    const modalTitle = document.getElementById('td-modal-title');
    const form = document.getElementById('td-form-indikator');
    const methodField = document.getElementById('td-method-field');
    const btnBukaTambah = document.getElementById('td-btn-buka-tambah');
    const btnClose = document.getElementById('td-modal-close');
    const btnCancel = document.getElementById('td-modal-cancel');

    const inpKode = document.getElementById('inp-kode');
    const inpNama = document.getElementById('inp-nama');
    const inpSub = document.getElementById('inp-sub');
    const inpSubjek = document.getElementById('inp-subjek');
    const inpOpd = document.getElementById('inp-opd');
    const inpSatuan = document.getElementById('inp-satuan');
    const inpKeterangan = document.getElementById('inp-keterangan');

    function bukaModalTambah() {
        modalTitle.textContent = 'Tambah Indikator Baru';
        form.action = "{{ route('tabel-dinamis.admin.indikator.store') }}";
        methodField.innerHTML = '';
        form.reset();
        modal.classList.add('active');
    }

    function tutupModal() {
        modal.classList.remove('active');
    }

    if (btnBukaTambah) btnBukaTambah.addEventListener('click', bukaModalTambah);
    if (btnClose) btnClose.addEventListener('click', tutupModal);
    if (btnCancel) btnCancel.addEventListener('click', tutupModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) tutupModal();
    });

    document.querySelectorAll('.td-btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            modalTitle.textContent = `Edit Indikator [${this.getAttribute('data-kode')}]`;
            form.action = "{{ url(config('tabel_dinamis.prefix', 'tabel-dinamis') . '/admin/indikator') }}/" + id;
            methodField.innerHTML = '@method("PUT")';

            inpKode.value = this.getAttribute('data-kode') || '';
            inpNama.value = this.getAttribute('data-nama') || '';
            inpSub.value = this.getAttribute('data-sub_indikator') || '';
            inpSubjek.value = this.getAttribute('data-subjek_id') || '';
            inpOpd.value = this.getAttribute('data-opd_id') || '';
            inpSatuan.value = this.getAttribute('data-satuan') || '';
            inpKeterangan.value = this.getAttribute('data-keterangan') || '';

            modal.classList.add('active');
        });
    });
});
</script>
@endpush


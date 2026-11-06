@extends($layout)

@section('title', 'Impor Data Excel — Admin Tabel Dinamis')

@push('styles')
<style>
    .td-admin-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
    }
    .td-import-card {
        background: var(--white);
        border: 1px solid rgba(15, 34, 64, 0.08);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(10, 22, 40, 0.04),
                    0 10px 24px -4px rgba(10, 22, 40, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 1);
        position: relative;
        overflow: hidden;
    }
    .td-import-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: linear-gradient(90deg, var(--deep-blue-600) 0%, var(--deep-blue-400) 100%);
    }
    .td-import-card h2 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--deep-blue-800);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.6rem;
        border-bottom: 2px solid var(--deep-blue-100);
    }
    .td-import-card h2 .td-h2-icon {
        width: 28px;
        height: 28px;
        background: var(--deep-blue-50);
        border-radius: var(--radius-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .td-error-list-card {
        border-left: 3px solid var(--danger);
    }
    .td-error-list-card h3 {
        color: var(--danger);
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .td-error-list-card ul {
        padding-left: 1.25rem;
        font-size: 0.85rem;
        color: var(--danger);
        max-height: 250px;
        overflow-y: auto;
    }
    .td-error-list-card ul li {
        margin-bottom: 0.3rem;
    }
    .td-notice-box {
        background: var(--warning-bg);
        border: 1px solid var(--warning-border);
        border-radius: var(--radius-sm);
        padding: 0.85rem 1rem;
        font-size: 0.85rem;
        color: var(--warning);
    }
    .td-notice-box strong {
        display: block;
        margin-bottom: 0.3rem;
    }
    .td-notice-box ul {
        padding-left: 1.2rem;
        margin-top: 0.3rem;
    }
    .td-notice-box li {
        margin-bottom: 0.25rem;
    }
    .td-file-input-wrapper {
        position: relative;
    }
    .td-file-input-wrapper input[type="file"] {
        padding: 0.6rem;
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-sm);
        background: var(--gray-50);
        cursor: pointer;
        transition: all var(--transition);
    }
    .td-file-input-wrapper input[type="file"]:hover {
        border-color: var(--deep-blue-400);
        background: var(--deep-blue-50);
    }
    .td-ref-table code {
        background: var(--deep-blue-50);
        color: var(--deep-blue-700);
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .td-ref-table .td-wajib {
        color: var(--danger);
        font-weight: 700;
        font-size: 0.78rem;
    }
    .td-ref-table .td-opsional {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 0.78rem;
    }
</style>
@endpush

@section('content')
<div class="td-admin-toolbar">
    <a href="{{ route('tabel-dinamis.index') }}" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
        ← Kembali ke Tabel Dinamis
    </a>
    <a href="{{ route('tabel-dinamis.admin.impor.template') }}" class="td-btn td-btn-secondary" style="font-size: 0.8rem;">
        📥 Unduh Template Excel (.xlsx)
    </a>
</div>

@if(session('success'))
    <div class="td-alert td-alert-success">
        ✓ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="td-alert td-alert-error">
        <strong>Gagal Mengimpor:</strong>&nbsp;{{ session('error') }}
    </div>
@endif

@if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="td-import-card td-error-list-card">
        <h3>
            ⚠ Daftar Kesalahan Validasi (Maksimal 100 Error Dilaporkan)
        </h3>
        <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.6rem;">
            Seluruh data dibatalkan secara atomik (tidak ada baris yang tersimpan) sampai semua kesalahan di bawah diperbaiki:
        </p>
        <ul>
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="td-import-card">
    <h2>
        <span class="td-h2-icon">📤</span>
        Impor Data Indikator (Excel)
    </h2>

    <div class="td-notice-box" style="margin-bottom: 1.25rem;">
        <strong>⚠ Perhatian & Keterbatasan Sistem:</strong>
        <ul>
            <li>Proses impor bersifat <strong>atomik</strong> (semua berhasil atau semua batal) dan <strong>idempoten</strong> (data dengan kombinasi yang sama akan diperbarui).</li>
            <li><strong>Keterbatasan:</strong> Proses impor hanya menambah dan memperbarui data, <u>tidak menghapus</u> data yang sudah ada sebelumnya di database.</li>
            <li>Tahun data hanya diperbolehkan antara <strong>{{ config('tabel_dinamis.tahun_min', 2021) }}</strong> sampai <strong>{{ config('tabel_dinamis.tahun_max', 2026) }}</strong>.</li>
            <li>Jenis data hanya diperbolehkan <strong>Realisasi</strong> atau <strong>Target</strong>.</li>
        </ul>
    </div>

    <form action="{{ route('tabel-dinamis.admin.impor.proses') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="td-form-group">
            <label for="file">Pilih Berkas Excel (.xlsx, Maksimal 5 MB)</label>
            <div class="td-file-input-wrapper">
                <input type="file" name="file" id="file" accept=".xlsx" required>
            </div>
            @error('file')
                <small style="color: var(--danger); display: block; margin-top: 0.35rem; font-size: 0.8rem;">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="td-btn td-btn-primary" style="margin-top: 0.5rem;">
            📤 Unggah & Proses Impor
        </button>
    </form>
</div>

<div class="td-import-card">
    <h2>
        <span class="td-h2-icon">📋</span>
        Format Standar Kolom Berkas Impor
    </h2>
    <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 0.85rem;">
        Pastikan baris pertama (header) berkas Excel Anda memuat kolom-kolom berikut:
    </p>

    <div class="td-table-wrap">
        <table class="td-table td-ref-table">
            <thead>
                <tr>
                    <th>Kolom Header</th>
                    <th>Status</th>
                    <th>Contoh Nilai</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>kode_indikator</code></td>
                    <td><span class="td-wajib">Wajib</span></td>
                    <td>CONTOH-KOMINFO-01</td>
                    <td>Harus sesuai dengan kode indikator yang terdaftar di database.</td>
                </tr>
                <tr>
                    <td><code>nama_wilayah</code></td>
                    <td><span class="td-wajib">Wajib</span></td>
                    <td>Kota Ternate</td>
                    <td>Harus sesuai dengan daftar nama kabupaten/kota resmi BPS Maluku Utara.</td>
                </tr>
                <tr>
                    <td><code>tahun</code></td>
                    <td><span class="td-wajib">Wajib</span></td>
                    <td>2023</td>
                    <td>Angka tahun antara 2021 s.d. 2026.</td>
                </tr>
                <tr>
                    <td><code>jenis</code></td>
                    <td><span class="td-wajib">Wajib</span></td>
                    <td>Realisasi</td>
                    <td>Hanya boleh diisi "Realisasi" atau "Target".</td>
                </tr>
                <tr>
                    <td><code>nilai</code></td>
                    <td><span class="td-opsional">Opsional</span></td>
                    <td>85.50</td>
                    <td>Boleh kosong/tanda strip (-) jika belum ada data.</td>
                </tr>
                <tr>
                    <td><code>sumber</code></td>
                    <td><span class="td-opsional">Opsional</span></td>
                    <td>BPS Prov Malut</td>
                    <td>Sumber atau catatan publikasi data.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="td-import-card">
    <h2>
        <span class="td-h2-icon">🕒</span>
        Riwayat & Log Impor Data (Audit Trail)
    </h2>
    <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 0.85rem;">
        Catatan 10 aktivitas impor terakhir untuk transparansi dan akuntabilitas pembaruan data daerah:
    </p>

    @if(isset($riwayat) && $riwayat->count() > 0)
        <div class="td-table-wrap">
            <table class="td-table">
                <thead>
                    <tr>
                        <th>Waktu Unggah</th>
                        <th>Nama Berkas</th>
                        <th>Ukuran</th>
                        <th>Oleh</th>
                        <th>Baris Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                        <tr>
                            <td style="white-space: nowrap; font-size: 0.8rem; color: var(--gray-600);">
                                {{ $r->created_at->format('d/m/Y H:i') }} WIT
                            </td>
                            <td>
                                <strong style="color: var(--deep-blue-800);">{{ $r->nama_berkas }}</strong>
                            </td>
                            <td>{{ $r->ukuran_kb }} KB</td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--gray-600);">{{ $r->diunggah_oleh ?? 'Admin' }}</span>
                            </td>
                            <td>
                                @if($r->status === 'SUKSES')
                                    <span style="font-weight: 700; color: var(--success);">{{ number_format($r->total_baris, 0, ',', '.') }} data</span>
                                @else
                                    <span style="color: var(--danger);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($r->status === 'SUKSES')
                                    <span style="background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700;">
                                        ✓ SUKSES
                                    </span>
                                @else
                                    <span style="background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700;" title="{{ $r->pesan }}">
                                        ✕ GAGAL
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 1.5rem; color: var(--gray-400); font-size: 0.85rem; border: 1.5px dashed var(--gray-200); border-radius: var(--radius-sm);">
            Belum ada riwayat aktivitas impor berkas yang tercatat.
        </div>
    @endif
</div>
@endsection

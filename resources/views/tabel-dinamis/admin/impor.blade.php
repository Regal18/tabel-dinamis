@extends($layout)

@section('title', 'Impor Data Excel — Admin Tabel Dinamis')

@section('content')
<div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
    <a href="{{ route('tabel-dinamis.index') }}" class="td-btn td-btn-outline" style="font-size: 0.8rem;">
        &larr; Kembali ke Tabel Dinamis
    </a>
    <a href="{{ route('tabel-dinamis.admin.impor.template') }}" class="td-btn td-btn-secondary" style="font-size: 0.8rem;">
        📥 Unduh Template Excel (.xlsx)
    </a>
</div>

@if(session('success'))
    <div class="td-alert td-alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="td-alert td-alert-error">
        <strong>Gagal Mengimpor:</strong> {{ session('error') }}
    </div>
@endif

@if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="td-card" style="border-left: 4px solid #e94560;">
        <h3 style="color: #e94560; font-size: 0.95rem; margin-bottom: 0.5rem;">
            Daftar Kesalahan Validasi (Maksimal 100 Error Dilaporkan):
        </h3>
        <p style="font-size: 0.85rem; color: #555; margin-bottom: 0.5rem;">
            Seluruh data dibatalkan secara atomik (tidak ada baris yang tersimpan) sampai semua kesalahan di bawah diperbaiki:
        </p>
        <ul style="padding-left: 1.25rem; font-size: 0.85rem; color: #721c24; max-height: 250px; overflow-y: auto;">
            @foreach(session('import_errors') as $err)
                <li style="margin-bottom: 0.25rem;">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="td-card">
    <h2>Impor Data Indikator (Excel / CSV)</h2>
    
    <div class="td-alert td-alert-warning" style="font-size: 0.85rem;">
        <strong>Perhatian & Keterbatasan Sistem:</strong>
        <ul style="padding-left: 1.2rem; margin-top: 0.3rem;">
            <li>Proses impor bersifat <strong>atomik</strong> (semua berhasil atau semua batal) dan <strong>idempoten</strong> (data dengan kombinasi indikator, wilayah, tahun, dan jenis yang sama akan diperbarui).</li>
            <li><strong>Keterbatasan:</strong> Proses impor hanya menambah dan memperbarui data, <u>tidak menghapus</u> data yang sudah ada sebelumnya di database.</li>
            <li>Tahun data hanya diperbolehkan antara <strong>{{ config('tabel_dinamis.tahun_min', 2021) }}</strong> sampai <strong>{{ config('tabel_dinamis.tahun_max', 2026) }}</strong>.</li>
            <li>Jenis data hanya diperbolehkan <strong>Realisasi</strong> atau <strong>Target</strong>.</li>
        </ul>
    </div>

    <form action="{{ route('tabel-dinamis.admin.impor.proses') }}" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
        @csrf

        <div class="td-form-group">
            <label for="file">Pilih Berkas Excel (.xlsx, Maksimal 5 MB)</label>
            <input type="file" name="file" id="file" accept=".xlsx" required>
            @error('file')
                <small style="color: #e94560; display: block; margin-top: 0.25rem;">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="td-btn td-btn-primary" style="margin-top: 0.5rem;">
            📤 Unggah & Proses Impor
        </button>
    </form>
</div>

<div class="td-card">
    <h2>Format Standar Kolom Berkas Impor</h2>
    <p style="font-size: 0.85rem; color: #555; margin-bottom: 0.75rem;">
        Pastikan baris pertama (header) berkas Excel Anda memuat kolom-kolom berikut:
    </p>

    <div class="td-table-wrap">
        <table class="td-table">
            <thead>
                <tr>
                    <th>Kolom Header</th>
                    <th>Wajib / Opsional</th>
                    <th>Contoh Nilai</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>kode_indikator</code></td>
                    <td><span style="color:#e94560; font-weight:bold;">Wajib</span></td>
                    <td>CONTOH-KOMINFO-01</td>
                    <td>Harus sesuai dengan kode indikator yang terdaftar di database.</td>
                </tr>
                <tr>
                    <td><code>nama_wilayah</code></td>
                    <td><span style="color:#e94560; font-weight:bold;">Wajib</span></td>
                    <td>Kota Ternate</td>
                    <td>Harus sesuai dengan daftar nama kabupaten/kota resmi BPS Maluku Utara.</td>
                </tr>
                <tr>
                    <td><code>tahun</code></td>
                    <td><span style="color:#e94560; font-weight:bold;">Wajib</span></td>
                    <td>2023</td>
                    <td>Angka tahun antara 2021 s.d. 2026.</td>
                </tr>
                <tr>
                    <td><code>jenis</code></td>
                    <td><span style="color:#e94560; font-weight:bold;">Wajib</span></td>
                    <td>Realisasi</td>
                    <td>Hanya boleh diisi "Realisasi" atau "Target".</td>
                </tr>
                <tr>
                    <td><code>nilai</code></td>
                    <td>Opsional</td>
                    <td>85.50</td>
                    <td>Boleh kosong/tanda strip (-) jika belum ada data.</td>
                </tr>
                <tr>
                    <td><code>sumber</code></td>
                    <td>Opsional</td>
                    <td>BPS Prov Malut / Diskominfo</td>
                    <td>Sumber atau catatan publikasi data.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

# Tabel Dinamis Indikator Bidang IPW — BAPPEDA Maluku Utara

Modul web terisolasi (*isolated module*) untuk penyajian data statistik dan indikator pembangunan 8 Organisasi Perangkat Daerah (OPD) Bidang Infrastruktur dan Pengembangan Wilayah (IPW) Badan Perencanaan Pembangunan Daerah (BAPPEDA) Provinsi Maluku Utara.

Modul ini dibangun sebagai aplikasi mandiri di Laravel yang siap diintegrasikan secara mulus ke aplikasi Web Taskboard IPW tanpa merusak arsitektur induk.

---

## 🚀 Fitur Utama

1. **Penyajian Tabel Dinamis (Meniru BPS & Enterprise Dashboard)**:
   - Alur filter: Subjek -> Indikator (pencarian realtime dengan debounce) -> Jenis Data (`Realisasi` atau `Target`) -> Tahun (rentang 2021–2026 dengan penanda visual `Tersedia` vs `Nihil`) -> Tambah ke Data Terpilih (fleksibel multi-indikator) -> Judul Baris (Wilayah) -> Tabel Hasil.
   - Tabel hasil dengan header dua tingkat, kolom pertama (*Kabupaten/Kota*) *sticky freeze*, format angka lokal Indonesia (`.` untuk ribuan dan `,` untuk desimal), nilai kosong/null ditampilkan `'-'`.
   - Interaksi tabel interaktif: **Pencarian cepat baris wilayah** dan **Pengurutan (sorting) kolom naik/turun** berindikator panah visual.
   - Baris ringkasan statistik otomatis di `<tfoot>`: **📊 Rata-rata Provinsi**, **▲ Nilai Tertinggi (Maks)**, dan **▼ Nilai Terendah (Min)**.
2. **Visualisasi Grafik Dinamis (Chart.js Interaktif)**:
   - Tab switcher instan antara **Tabel Data** dan **Visualisasi Grafik**.
   - Mode Grafik Batang (*Bar Chart* perbandingan wilayah per tahun) dan Grafik Garis (*Line Chart* tren tahunan 2021–2026 dilengkapi garis putus-putus Rata-rata Provinsi Maluku Utara).
   - Fitur unduh gambar grafik beresolusi tinggi format PNG.
3. **Ekspor Data & Cetak Laporan Resmi**:
   - Format `.xlsx`: Menggabungkan sel header (*merged cells*), nilai angka tetap bertipe numerik asli, *freeze pane* pada sel `B3`, dan catatan kaki sumber data.
   - Format `.csv`: Dilengkapi UTF-8 BOM dan pemisah titik koma (`;`).
   - Fitur **Cetak / PDF Resmi**: Dilengkapi Kop Surat Resmi Pemerintah Provinsi Maluku Utara - BAPPEDA Bidang IPW.
   - Dilindungi rate limiting 10 permintaan per menit.
4. **Manajemen Master Data Indikator (CRUD Admin)**:
   - Antarmuka web khusus admin untuk melihat, mencari, menambah, mengedit, dan menghapus data indikator secara aman.
   - Terintegrasi dengan sistem caching referensi otomatis (`Cache::remember` & `Cache::forget`).
5. **Impor Berkas Excel Admin & Audit Trail**:
   - Dilindungi autentikasi dan Gate `kelola-tabel-dinamis`.
   - Proses **atomik** (DB Transaction: jika ada 1 baris salah, seluruh proses dibatalkan) dan **idempoten** (*upsert*).
   - Pelaporan galat menyebutkan nomor baris Excel asli (maksimal 100 galat).
   - Menyediakan fitur unduh template berkas Excel resmi.
   - **Audit Trail Log Riwayat Impor**: Mencatat log setiap aktivitas unggah berkas (sukses/gagal, ukuran berkas, total baris, pengunggah).
6. **Kesiapan Mobile (Stateless Backend API)**:
   - Seluruh logika bisnis dipusatkan pada Service layer (`TabelService`, `ImportExcelService`, `ExportService`).
   - Endpoint REST API bebas dari session state dan siap digunakan oleh aplikasi Flutter.

---

## 📋 Checklist Keamanan (*Security Hardening*)

- [x] **Tidak Ada Kredensial di Kode**: Akun admin dan rahasia basis data sepenuhnya dibaca dari berkas `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`).
- [x] **Mode Produksi (`APP_DEBUG=false`)**: Pada lingkungan produksi, selalu setel `APP_DEBUG=false` agar informasi jejak panggilan (*stack trace*) dan kredensial sistem tidak terekspos.
- [x] **Proteksi CSRF**: Seluruh formulir web dan AJAX POST menyertakan header token CSRF `X-CSRF-TOKEN`.
- [x] **Rate Limiting (Throttle)**:
  - Query Filter & Data: 60 permintaan/menit (`throttle:60,1`).
  - Unduh Berkas Export: 10 permintaan/menit (`throttle:10,1`).
- [x] **Validasi Input Berlapis**: Validasi dilakukan di sisi klien (JS) dan di sisi server menggunakan `TabelRequest` dan Form Validation.
- [x] **Netralitas Database**: Penamaan tabel menggunakan awalan `td_` (`td_opd`, `td_subjek`, `td_wilayah`, `td_indikator`, `td_data_indikator`) dan tipe data netral yang kompatibel di PostgreSQL, MySQL, maupun SQLite.

---

## 🛠️ Instalasi & Menjalankan Lokal

### 1. Klon Repositori & Instal Dependensi
```powershell
git clone https://github.com/Regal18/tabel-dinamis.git
cd tabel-dinamis
composer install
```

### 2. Konfigurasi Lingkungan (.env)
Salin berkas `.env.example` ke `.env`:
```powershell
copy .env.example .env
php artisan key:generate
```
Sesuaikan konfigurasi admin di `.env`:
```env
ADMIN_EMAIL=admin@tabeldinamis.test
ADMIN_PASSWORD=rahasia123
TD_ROUTE_PREFIX=tabel-dinamis
```

### 3. Migrasi & Seeder Basis Data
Jalankan migrasi tabel `td_*` dan data awal:
```powershell
php artisan migrate --seed --seeder="Database\Seeders\TabelDinamisSeeder"
```

### 4. Menjalankan Server Lokal
```powershell
php artisan serve
```
Akses aplikasi melalui peramban web:
- **Halaman Utama Tabel Dinamis**: [http://127.0.0.1:8000/tabel-dinamis](http://127.0.0.1:8000/tabel-dinamis) (atau langsung [http://127.0.0.1:8000](http://127.0.0.1:8000))
- **Halaman Admin Impor Excel**: [http://127.0.0.1:8000/tabel-dinamis/admin/impor](http://127.0.0.1:8000/tabel-dinamis/admin/impor)  
  *(Gunakan autentikasi Basic Auth sesuai kredensial di `.env`)*

---

## 🧪 Menjalankan Pengujian Otomatis

Rangkaian tes otomatis mencakup tes Unit, Feature API, Web Page, Uji Penerimaan Kriteria SKPL Bagian 3.4, dan Admin CRUD Master Data.

```powershell
# Menjalankan seluruh pengujian (86 tests, 100% PASS)
php artisan test

# Menjalankan uji penerimaan SKPL 3.4
php artisan test --filter=SkplAcceptanceTest

# Menjalankan pengujian per modul
php artisan test --filter=TabelServiceTest
php artisan test --filter=ImportExcelServiceTest
php artisan test --filter=ExportApiTest
```

---

## 📖 Dokumentasi Lengkap
- [Spesifikasi Kontrak REST API](docs/api.md) — Dokumen panduan untuk integrasi aplikasi web dan mobile Flutter.
- [Panduan Titik Integrasi Taskboard](docs/integrasi.md) — Rincian 5 titik konfigurasi untuk penggabungan modul ke aplikasi taskboard BAPPEDA.

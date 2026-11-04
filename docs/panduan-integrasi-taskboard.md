# Panduan Langkah Demi Langkah: Integrasi Modul Tabel Dinamis ke Web Taskboard IPW

Dokumen ini adalah panduan teknis resmi (**FASE W8**) bagi pengembang tim web taskboard IPW BAPPEDA Provinsi Maluku Utara untuk menggabungkan modul terisolasi ini ke dalam aplikasi Laravel taskboard.

---

## 📑 Daftar Isi
1. [Langkah 1: Penyalinan Berkas Modul](#langkah-1-penyalinan-berkas-modul)
2. [Langkah 2: Pendaftaran Service Provider](#langkah-2-pendaftaran-service-provider)
3. [Langkah 3: Eksekusi Migrasi Basis Data](#langkah-3-eksekusi-migrasi-basis-data)
4. [Langkah 4: Konfigurasi 5 Titik Integrasi](#langkah-4-konfigurasi-5-titik-integrasi)
5. [Langkah 5: Pelepasan Ketergantungan Mode Mandiri](#langkah-5-pelepasan-ketergantungan-mode-mandiri)
6. [Langkah 6: Menjalankan Seluruh Pengujian Modul](#langkah-6-menjalankan-seluruh-pengujian-modul)
7. [Langkah 7: Prosedur Smoke Test](#langkah-7-prosedur-smoke-test)

---

## Langkah 1: Penyalinan Berkas Modul

Salin direktori dan berkas berikut dari repositori ini ke dalam direktori aplikasi web taskboard:

| Dari Repositori Modul | Ke Aplikasi Taskboard | Keterangan |
| :--- | :--- | :--- |
| `app/Modules/TabelDinamis/` | `app/Modules/TabelDinamis/` | Logika modul (Models, Services, Controllers, Requests, Exceptions, Provider) |
| `config/tabel_dinamis.php` | `config/tabel_dinamis.php` | Berkas konfigurasi utama modul |
| `routes/tabel_dinamis.php` | `routes/tabel_dinamis.php` | Rute terisolasi web, admin, dan API modul |
| `resources/views/tabel-dinamis/` | `resources/views/tabel-dinamis/` | Berkas tampilan Blade (`index.blade.php`, `admin/impor.blade.php`, `layout.blade.php`) |
| `database/migrations/2024_01_01_00001*.php` | `database/migrations/` | 5 berkas migrasi tabel berawalan `td_` |
| `database/seeders/TabelDinamisSeeder.php` | `database/seeders/` | Seeder referensi 8 OPD IPW dan 10 Kab/Kota Maluku Utara |
| `tests/Unit/TabelDinamis/` | `tests/Unit/TabelDinamis/` | Seluruh unit test modul |
| `tests/Feature/TabelDinamis/` | `tests/Feature/TabelDinamis/` | Seluruh feature & acceptance test modul |

Pastikan pustaka spreadsheet terpasang pada `composer.json` aplikasi taskboard:
```bash
composer require phpoffice/phpspreadsheet
```

---

## Langkah 2: Pendaftaran Service Provider

Daftarkan `TabelDinamisServiceProvider` di dalam berkas konfigurasi provider aplikasi taskboard:

- **Laravel 11 / 12** (`bootstrap/providers.php`):
  ```php
  return [
      App\Providers\AppServiceProvider::class,
      // Tambahkan ServiceProvider modul Tabel Dinamis:
      \App\Modules\TabelDinamis\Providers\TabelDinamisServiceProvider::class,
  ];
  ```

---

## Langkah 3: Eksekusi Migrasi Basis Data

Jalankan perintah migrasi:
```bash
php artisan migrate
```

### 🔒 Verifikasi Bebas Bentrok Nama Tabel:
Seluruh tabel modul ini secara ketat menggunakan awalan namespace `td_`, sehingga dijamin **tidak akan bentrok** dengan tabel bawaan taskboard (seperti `users`, `tasks`, `projects`, dsb.):
1. `td_opd`
2. `td_subjek`
3. `td_wilayah`
4. `td_indikator`
5. `td_data_indikator`

Jalankan seeder master referensi 8 OPD dan wilayah Maluku Utara:
```bash
php artisan db:seed --class="Database\Seeders\TabelDinamisSeeder"
```

---

## Langkah 4: Konfigurasi 5 Titik Integrasi

Buka berkas `config/tabel_dinamis.php` pada aplikasi taskboard dan sesuaikan 5 titik integrasi:

### Titik 1: Middleware Akses Baca (`middleware_baca`)
Ubah dari kosong `[]` menjadi middleware autentikasi taskboard:
```php
'middleware_baca' => ['auth'],
```
*(Pengunjung publik/belum login akan dialihkan ke halaman login taskboard)*.

### Titik 2: Middleware Akses Admin (`middleware_admin`)
Hapus `auth.basic` dan gunakan session login taskboard:
```php
'middleware_admin' => ['auth', 'can:kelola-tabel-dinamis'],
```

### Titik 3: Gate Otorisasi (`kelola-tabel-dinamis`)
Buka `app/Modules/TabelDinamis/Providers/TabelDinamisServiceProvider.php` pada method `daftarkanGate()`. Ganti pengecekan email tunggal dengan sistem hak akses / role taskboard:
```php
protected function daftarkanGate(): void
{
    Gate::define('kelola-tabel-dinamis', function ($user) {
        // Contoh jika menggunakan kolom 'role' pada model User taskboard:
        return in_array($user->role, ['admin', 'superadmin', 'bappeda_admin']);

        // Atau jika menggunakan Spatie Laravel Permission:
        // return $user->can('kelola-tabel-dinamis') || $user->hasRole('admin');
    });
}
```

### Titik 4: Layout Template Blade (`layout`)
Ubah layout mandiri menjadi layout template web taskboard Anda:
```php
'layout' => 'layouts.app', // atau 'layouts.taskboard' / 'adminlte::page'
```
*Pastikan layout induk taskboard memuat:*
- `<meta name="csrf-token" content="{{ csrf_token() }}">`
- `@yield('content')`
- `@stack('styles')` dan `@stack('scripts')`

### Titik 5: Prefix URL Rute (`prefix`)
Sesuaikan prefix URL rute jika ingin diseragamkan dengan modul taskboard lainnya:
```php
'prefix' => env('TD_ROUTE_PREFIX', 'ipw/tabel-dinamis'),
```

---

## Langkah 5: Pelepasan Ketergantungan Mode Mandiri

Karena sistem taskboard sudah memiliki manajemen akun pengguna dan sistem login sendiri:
1. **Hapus / Abaikan variabel admin `.env` mandiri**:
   Variabel `ADMIN_EMAIL` dan `ADMIN_PASSWORD` tidak lagi diperlukan di `.env`.
2. **Hapus seeder akun admin**:
   Pada `database/seeders/TabelDinamisSeeder.php`, method `seedAdmin()` tidak perlu dijalankan lagi karena autentikasi sudah sepenuhnya dikelola oleh tabel `users` milik taskboard.
3. **Pelepasan `auth.basic`**:
   Dengan mengubah `middleware_admin` menjadi `['auth', 'can:kelola-tabel-dinamis']`, form impor admin tidak lagi meminta pop-up browser Basic Auth, melainkan langsung menggunakan sesi login taskboard yang sedang aktif.

---

## Langkah 6: Menjalankan Seluruh Pengujian Modul

Jalankan test suite modul di dalam direktori aplikasi taskboard:
```bash
php artisan test --filter=TabelDinamis
```
Pastikan seluruh tes menunjukkan status **PASS** (100% hijau).

---

## Langkah 7: Prosedur Smoke Test

Lakukan pengujian manual akhir (*Smoke Test*) di peramban web:

1. **Tes Akses Tamu (Belum Login)**:
   - Akses URL `http://your-taskboard.test/tabel-dinamis`
   - *Ekspektasi*: Dialihkan (*redirect*) ke halaman `/login`.
2. **Tes Akses Staf / Non-Admin**:
   - Login dengan akun pengguna non-admin (misal: staf OPD).
   - Akses `http://your-taskboard.test/tabel-dinamis`.
   - Lakukan pemilihan Subjek, pencarian Indikator, centang Tahun & Wilayah, lalu klik **Tambah**.
   - Klik **Submit**: Tabel hasil dinamis dua tingkat harus tampil dengan benar.
   - Klik **Unduh Excel (.xlsx)** dan **Unduh CSV**: Berkas harus terunduh dengan isi data yang sama.
   - Coba akses menu admin impor: `http://your-taskboard.test/tabel-dinamis/admin/impor`.
   - *Ekspektasi*: Sistem menolak dengan halaman **403 Forbidden**.
3. **Tes Akses Administrator**:
   - Login dengan akun berstatus Admin taskboard.
   - Buka menu `http://your-taskboard.test/tabel-dinamis/admin/impor`.
   - *Ekspektasi*: Halaman impor terbuka (status 200 OK).
   - Klik **Unduh Template Excel (.xlsx)** untuk memeriksa unduhan berkas panduan.
   - Unggah berkas Excel data indikator.
   - *Ekspektasi*: Tampil notifikasi hijau berhasil, dan data baru langsung muncul di tabel dinamis.


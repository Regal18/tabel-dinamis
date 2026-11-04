# Panduan Titik Integrasi Modul Tabel Dinamis ke Web Taskboard IPW

Dokumen ini memuat **5 Titik Integrasi** resmi untuk memindahkan modul terisolasi ini ke dalam aplikasi web taskboard BAPPEDA Provinsi Maluku Utara.

---

## Daftar Titik Integrasi (`config/tabel_dinamis.php`)

Seluruh pengaturan integrasi berada di satu berkas konfigurasi [`config/tabel_dinamis.php`](../config/tabel_dinamis.php):

### Titik 1: Middleware Akses Baca (`middleware_baca`)
Mengatur pengamanan halaman web utama dan API baca (subjek, indikator, tahun, wilayah, tabel dinamis, export).
- **Mode Mandiri (Saat Ini)**:
  ```php
  'middleware_baca' => [],
  ```
  *(Terbuka tanpa login untuk keperluan pengembangan lokal)*.
- **Saat Digabung ke Taskboard**:
  ```php
  'middleware_baca' => ['auth'],
  ```
  *(Hanya pengguna terautentikasi aplikasi taskboard yang dapat membuka modul)*.

---

### Titik 2: Middleware Akses Admin (`middleware_admin`)
Mengatur pengamanan halaman formulir dan proses impor berkas Excel.
- **Mode Mandiri (Saat Ini)**:
  ```php
  'middleware_admin' => ['auth.basic', 'can:kelola-tabel-dinamis'],
  ```
- **Saat Digabung ke Taskboard**:
  ```php
  'middleware_admin' => ['auth', 'can:kelola-tabel-dinamis'],
  ```
  *(Menggunakan sesi login web taskboard dan hak otorisasi Gate)*.

---

### Titik 3: Definisi Gate Otorisasi (`kelola-tabel-dinamis`)
Berada di [`app/Modules/TabelDinamis/Providers/TabelDinamisServiceProvider.php`](../app/Modules/TabelDinamis/Providers/TabelDinamisServiceProvider.php).
- **Mode Mandiri (Saat Ini)**:
  ```php
  Gate::define('kelola-tabel-dinamis', function ($user) {
      $adminEmail = env('ADMIN_EMAIL');
      return $adminEmail && $user->email === $adminEmail;
  });
  ```
- **Saat Digabung ke Taskboard**:
  Ganti dengan pengecekan role / permission bawaan sistem taskboard:
  ```php
  Gate::define('kelola-tabel-dinamis', function ($user) {
      // Contoh jika memakai Spatie Permission atau kolom role:
      return $user->hasRole('admin-ipw') || $user->can('kelola-indikator');
  });
  ```

---

### Titik 4: Layout Template Blade (`layout`)
Menentukan layout pembungkus halaman filter dan halaman impor admin.
- **Mode Mandiri (Saat Ini)**:
  ```php
  'layout' => 'tabel-dinamis::layout',
  ```
  *(Memakai layout mobile-first bawaan modul)*.
- **Saat Digabung ke Taskboard**:
  ```php
  'layout' => 'layouts.app', // atau 'adminlte::page'
  ```
  **Syarat Layout Induk:**
  1. Memuat tag meta CSRF: `<meta name="csrf-token" content="{{ csrf_token() }}">`
  2. Memuat yield konten: `@yield('content')`
  3. Memuat stack aset: `@stack('styles')` dan `@stack('scripts')`

---

### Titik 5: Prefix URL Rute (`prefix`)
Menentukan awalan URL seluruh rute modul.
- **Mode Mandiri (Saat Ini)**:
  ```php
  'prefix' => env('TD_ROUTE_PREFIX', 'tabel-dinamis'),
  ```
  *(URL: `/tabel-dinamis`, API: `/tabel-dinamis/api/*`, Admin: `/tabel-dinamis/admin/*`)*.
- **Saat Digabung ke Taskboard**:
  Dapat disesuaikan melalui berkas `.env` aplikasi taskboard tanpa mengubah kode program:
  ```env
  TD_ROUTE_PREFIX="ipw/tabel-dinamis"
  ```

---

## Langkah-Langkah Pemindahan Berkas ke Taskboard

1. Salin direktori modul terisolasi:
   - `app/Modules/TabelDinamis/` -> `app/Modules/TabelDinamis/`
   - `config/tabel_dinamis.php` -> `config/tabel_dinamis.php`
   - `routes/tabel_dinamis.php` -> `routes/tabel_dinamis.php`
   - `resources/views/tabel-dinamis/` -> `resources/views/tabel-dinamis/`
   - `database/migrations/2024_01_01_00001*` -> `database/migrations/`
2. Daftarkan ServiceProvider di `bootstrap/providers.php` aplikasi taskboard:
   ```php
   App\Modules\TabelDinamis\Providers\TabelDinamisServiceProvider::class,
   ```
3. Pastikan dependensi spreadsheet terpasang:
   ```bash
   composer require phpoffice/phpspreadsheet
   ```
4. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```


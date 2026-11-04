# Panduan Integrasi Modul Tabel Dinamis ke Web Taskboard IPW

Modul ini dibangun secara terisolasi agar mudah dipindahkan dan digabungkan ke aplikasi web taskboard BAPPEDA Provinsi Maluku Utara.

---

## 1. Daftar Berkas Modul yang Perlu Disalin
Salin folder dan berkas berikut ke repositori aplikasi taskboard:

```text
app/Modules/TabelDinamis/            ---> app/Modules/TabelDinamis/
config/tabel_dinamis.php             ---> config/tabel_dinamis.php
routes/tabel_dinamis.php             ---> routes/tabel_dinamis.php
resources/views/tabel-dinamis/       ---> resources/views/tabel-dinamis/
database/migrations/2024_01_01_00001* ---> database/migrations/
docs/api.md                          ---> docs/api.md
```

Pastikan dependensi berikut terpasang di `composer.json` proyek taskboard:
```bash
composer require phpoffice/phpspreadsheet
```

---

## 2. Pendaftaran Service Provider
Di Laravel 11/12, daftarkan `TabelDinamisServiceProvider` di `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    // Tambahkan provider modul Tabel Dinamis:
    App\Modules\TabelDinamis\Providers\TabelDinamisServiceProvider::class,
];
```

---

## 3. Titik Integrasi Konfigurasi (`config/tabel_dinamis.php`)
Ketika digabungkan ke aplikasi induk taskboard, ubah 5 titik integrasi pada `config/tabel_dinamis.php`:

### Titik 1: Middleware Baca
- **Mode Mandiri**: `[]` (terbuka untuk pengujian lokal)
- **Mode Taskboard**: Ganti menjadi `['auth']` agar hanya pengguna terdaftar taskboard yang dapat mengakses.

### Titik 2: Middleware Admin
- **Mode Mandiri**: `['auth.basic', 'can:kelola-tabel-dinamis']`
- **Mode Taskboard**: Ganti menjadi `['auth', 'can:kelola-tabel-dinamis']` (memakai sesi login taskboard).

### Titik 3: Otorisasi Gate `kelola-tabel-dinamis`
Buka `app/Modules/TabelDinamis/Providers/TabelDinamisServiceProvider.php` pada method `daftarkanGate()`:
- **Mode Mandiri**: Memeriksa `env('ADMIN_EMAIL')`.
- **Mode Taskboard**: Sesuaikan dengan sistem role atau permission aplikasi taskboard, misalnya:
```php
Gate::define('kelola-tabel-dinamis', function ($user) {
    return $user->hasRole('admin') || $user->can('manage-ipw-indicators');
});
```

### Titik 4: Layout Blade Induk
- **Mode Mandiri**: `'tabel-dinamis::layout'` (layout mandiri mobile-first bawaan modul).
- **Mode Taskboard**: Ganti dengan layout aplikasi taskboard Anda, misalnya `'layouts.app'` atau `'adminlte::page'`. Layout induk wajib menyediakan:
  - Meta tag CSRF: `<meta name="csrf-token" content="{{ csrf_token() }}">`
  - Kontainer yield: `@yield('content')`
  - Stack styles & scripts: `@stack('styles')` dan `@stack('scripts')`

### Titik 5: Prefix Rute
Jika ingin mengubah URL modul dari `/tabel-dinamis` menjadi `/ipw/tabel-dinamis`, ubah nilai `'prefix'` atau atur di `.env`:
```env
TD_ROUTE_PREFIX="ipw/tabel-dinamis"
```

---

## 4. Eksekusi Migrasi & Seeder Awal
Jalankan migrasi untuk membuat tabel berawalan `td_`:
```bash
php artisan migrate
```
Jika ingin mengimpor referensi 8 OPD bidang IPW dan daftar kabupaten/kota Maluku Utara:
```bash
php artisan db:seed --class=Database\Seeders\TabelDinamisSeeder
```

> **Catatan Validasi Data Riil:**
> Pastikan daftar nama wilayah pada tabel `td_wilayah` dicocokkan kembali dengan ejaan baris publikasi BPS Maluku Utara sebelum data resmi diimpor.


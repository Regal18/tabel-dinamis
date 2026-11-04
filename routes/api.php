<?php

/**
 * Rute API Stateless untuk Klien Mobile (Flutter) & Integrasi Sistem Eksternal.
 *
 * Menerapkan spesifikasi Fase W7:
 * - Menggunakan Controller YANG SAMA dengan versi web (tanpa duplikasi logika).
 * - Stateless: Tidak memerlukan session cookies atau token CSRF.
 * - Opsi Kebijakan Keamanan: Tanpa auth (data publik indikator) dengan throttle ketat.
 *   (60 req/menit untuk data referensi & tabel, 10 req/menit untuk ekspor berkas).
 */

use Illuminate\Support\Facades\Route;
use App\Modules\TabelDinamis\Http\Controllers\Api\FilterController;
use App\Modules\TabelDinamis\Http\Controllers\Api\TabelController;
use App\Modules\TabelDinamis\Http\Controllers\Api\ExportController;

$prefix = config('tabel_dinamis.prefix', 'tabel-dinamis');

// Helper makro pendaftaran rute agar tidak ada duplikasi kode
$daftarkanRuteApi = function () {
    // Rute Data & Referensi (Throttle 60 per menit)
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/subjek', [FilterController::class, 'subjek'])->name('api.mobile.subjek');
        Route::get('/indikator', [FilterController::class, 'indikator'])->name('api.mobile.indikator');
        Route::get('/tahun', [FilterController::class, 'tahun'])->name('api.mobile.tahun');
        Route::get('/wilayah', [FilterController::class, 'wilayah'])->name('api.mobile.wilayah');
        Route::post('/tabel', [TabelController::class, 'tabel'])->name('api.mobile.tabel');
    });

    // Rute Ekspor Berkas (Throttle ketat 10 per menit)
    Route::post('/export', [ExportController::class, 'export'])
        ->middleware('throttle:10,1')
        ->name('api.mobile.export');
};

// 1. Jalur langsung di bawah /api/* (misal: /api/subjek, /api/tabel, dll.)
$daftarkanRuteApi();

// 2. Jalur dengan sub-prefix (misal: /api/tabel-dinamis/subjek, /api/tabel-dinamis/tabel, dll.)
if ($prefix) {
    Route::prefix($prefix)->group($daftarkanRuteApi);
}

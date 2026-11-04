
<?php

/**
 * Rute Modul Tabel Dinamis.
 *
 * File ini dimuat oleh TabelDinamisServiceProvider di dalam grup middleware 'web'
 * dengan prefix dari config('tabel_dinamis.prefix') (default: 'tabel-dinamis').
 */

use Illuminate\Support\Facades\Route;
use App\Modules\TabelDinamis\Http\Controllers\TabelController;
use App\Modules\TabelDinamis\Http\Controllers\ImporController;
use App\Modules\TabelDinamis\Http\Controllers\Api\ReferensiController;
use App\Modules\TabelDinamis\Http\Controllers\Api\TabelApiController;

$middlewareBaca = config('tabel_dinamis.middleware_baca', []);
$middlewareAdmin = config('tabel_dinamis.middleware_admin', ['auth.basic', 'can:kelola-tabel-dinamis']);

/*
|--------------------------------------------------------------------------
| Halaman Web (Blade)
|--------------------------------------------------------------------------
*/
Route::middleware($middlewareBaca)->group(function () {
    Route::get('/', [TabelController::class, 'index'])->name('tabel-dinamis.index');
});

/*
|--------------------------------------------------------------------------
| Endpoint API (JSON & Export)
| Awalan: /{prefix}/api
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware($middlewareBaca)->group(function () {
    Route::get('/subjek', [ReferensiController::class, 'subjek'])->name('tabel-dinamis.api.subjek');
    Route::get('/indikator', [ReferensiController::class, 'indikator'])->name('tabel-dinamis.api.indikator');
    Route::get('/tahun', [ReferensiController::class, 'tahun'])->name('tabel-dinamis.api.tahun');
    Route::get('/wilayah', [ReferensiController::class, 'wilayah'])->name('tabel-dinamis.api.wilayah');
    Route::post('/tabel', [TabelApiController::class, 'tabel'])->name('tabel-dinamis.api.tabel');
    Route::post('/export', [TabelApiController::class, 'export'])->name('tabel-dinamis.api.export');
});

/*
|--------------------------------------------------------------------------
| Halaman & Aksi Admin (Impor Excel)
| Awalan: /{prefix}/admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware($middlewareAdmin)->group(function () {
    Route::get('/impor', [ImporController::class, 'form'])->name('tabel-dinamis.admin.impor.form');
    Route::post('/impor', [ImporController::class, 'proses'])->name('tabel-dinamis.admin.impor.proses');
});


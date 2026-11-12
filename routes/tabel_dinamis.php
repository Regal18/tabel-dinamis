<?php

/**
 * Rute Modul Tabel Dinamis.
 *
 * File ini dimuat oleh TabelDinamisServiceProvider di dalam grup middleware 'web'
 * dengan prefix dari config('tabel_dinamis.prefix') (default: 'tabel-dinamis').
 */

use Illuminate\Support\Facades\Route;
use App\Modules\TabelDinamis\Http\Controllers\BerandaController;
use App\Modules\TabelDinamis\Http\Controllers\TabelController;
use App\Modules\TabelDinamis\Http\Controllers\ImportController;
use App\Modules\TabelDinamis\Http\Controllers\Admin\IndikatorCrudController;
use App\Modules\TabelDinamis\Http\Controllers\Api\FilterController;
use App\Modules\TabelDinamis\Http\Controllers\Api\TabelController as ApiTabelController;
use App\Modules\TabelDinamis\Http\Controllers\Api\ExportController;

$middlewareBaca = config('tabel_dinamis.middleware_baca', []);
$middlewareAdmin = config('tabel_dinamis.middleware_admin', ['auth.basic', 'can:kelola-tabel-dinamis']);

/*
|--------------------------------------------------------------------------
| Halaman Web (Blade)
|--------------------------------------------------------------------------
*/
Route::middleware($middlewareBaca)->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('tabel-dinamis.beranda');
    Route::get('/', [TabelController::class, 'index'])->name('tabel-dinamis.index');
});

/*
|--------------------------------------------------------------------------
| Endpoint API (JSON & Export)
| Awalan: /{prefix}/api
| Dilindungi middleware_baca. Endpoint query throttle 60/menit, export 10/menit.
| Dikecualikan dari validasi CSRF karena merupakan endpoint query data publik & export.
|--------------------------------------------------------------------------
*/
Route::prefix('api')
    ->middleware($middlewareBaca)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->group(function () {
        Route::middleware('throttle:60,1')->group(function () {
            Route::get('/subjek', [FilterController::class, 'subjek'])->name('tabel-dinamis.api.subjek');
            Route::get('/indikator', [FilterController::class, 'indikator'])->name('tabel-dinamis.api.indikator');
            Route::get('/tahun', [FilterController::class, 'tahun'])->name('tabel-dinamis.api.tahun');
            Route::get('/wilayah', [FilterController::class, 'wilayah'])->name('tabel-dinamis.api.wilayah');
            Route::post('/tabel', [ApiTabelController::class, 'tabel'])->name('tabel-dinamis.api.tabel');
        });

        // Throttle 10 per menit khusus Export (Fase W5)
        Route::post('/export', [ExportController::class, 'export'])
            ->middleware('throttle:10,1')
            ->name('tabel-dinamis.api.export');
    });

/*
|--------------------------------------------------------------------------
| Halaman & Aksi Admin (Impor Excel)
| Awalan: /{prefix}/admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware($middlewareAdmin)->group(function () {
    // Impor Berkas Excel Indikator
    Route::get('/impor', [ImportController::class, 'form'])->name('tabel-dinamis.admin.impor.form');
    Route::post('/impor', [ImportController::class, 'proses'])->name('tabel-dinamis.admin.impor.proses');
    Route::get('/impor/template', [ImportController::class, 'template'])->name('tabel-dinamis.admin.impor.template');

    // Manajemen Master Data Indikator (CRUD)
    Route::resource('indikator', IndikatorCrudController::class)
        ->except(['create', 'show', 'edit'])
        ->names('tabel-dinamis.admin.indikator');
});


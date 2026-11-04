<?php

namespace App\Modules\TabelDinamis\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Tabel Dinamis.
 *
 * Mendaftarkan config, rute (di dalam grup web + prefix), view, dan Gate.
 * Ini adalah satu-satunya titik kontak antara modul dan aplikasi induk.
 */
class TabelDinamisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Gabungkan config bawaan modul
        $this->mergeConfigFrom(
            config_path('tabel_dinamis.php'),
            'tabel_dinamis'
        );
    }

    public function boot(): void
    {
        $this->daftarkanGate();
        $this->daftarkanRute();
        $this->daftarkanView();
    }

    /**
     * Mendaftarkan Gate 'kelola-tabel-dinamis'.
     *
     * Mode mandiri: cek email user sama dengan ADMIN_EMAIL di .env.
     * Mode taskboard: ganti isi Gate ini dengan pengecekan role taskboard.
     */
    protected function daftarkanGate(): void
    {
        Gate::define('kelola-tabel-dinamis', function ($user) {
            $adminEmail = env('ADMIN_EMAIL');

            return $adminEmail && $user->email === $adminEmail;
        });
    }

    /**
     * Mendaftarkan rute dari routes/tabel_dinamis.php,
     * dibungkus dalam grup middleware 'web' dengan prefix dari config.
     */
    protected function daftarkanRute(): void
    {
        Route::middleware('web')
            ->prefix(config('tabel_dinamis.prefix', 'tabel-dinamis'))
            ->group(base_path('routes/tabel_dinamis.php'));
    }

    /**
     * Mendaftarkan namespace view 'tabel-dinamis'.
     */
    protected function daftarkanView(): void
    {
        $this->loadViewsFrom(
            resource_path('views/tabel-dinamis'),
            'tabel-dinamis'
        );
    }
}


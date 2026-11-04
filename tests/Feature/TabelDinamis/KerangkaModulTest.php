<?php

namespace Tests\Feature\TabelDinamis;

use App\Models\User;
use App\Modules\TabelDinamis\Providers\TabelDinamisServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes Fase W0: kerangka modul — provider termuat dan Gate berfungsi.
 */
class KerangkaModulTest extends TestCase
{
    use RefreshDatabase;

    // ─── Provider ─────────────────────────────────────────────

    public function test_provider_terdaftar_dan_termuat(): void
    {
        // Pastikan TabelDinamisServiceProvider ada di daftar provider yang dimuat
        $loadedProviders = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(
            TabelDinamisServiceProvider::class,
            $loadedProviders,
            'TabelDinamisServiceProvider harus terdaftar di bootstrap/providers.php dan termuat.'
        );
    }

    public function test_config_tabel_dinamis_termuat(): void
    {
        // Pastikan config sudah ter-merge
        $this->assertNotNull(config('tabel_dinamis.prefix'));
        $this->assertEquals('tabel-dinamis', config('tabel_dinamis.prefix'));
    }

    public function test_config_layout_menunjuk_layout_modul(): void
    {
        $this->assertEquals('tabel-dinamis::layout', config('tabel_dinamis.layout'));
    }

    public function test_config_middleware_baca_kosong_mode_mandiri(): void
    {
        // Mode mandiri: halaman baca terbuka tanpa login
        $this->assertEquals([], config('tabel_dinamis.middleware_baca'));
    }

    public function test_config_middleware_admin_mode_mandiri(): void
    {
        $expected = ['auth.basic', 'can:kelola-tabel-dinamis'];
        $this->assertEquals($expected, config('tabel_dinamis.middleware_admin'));
    }

    public function test_view_namespace_terdaftar(): void
    {
        // Pastikan namespace view 'tabel-dinamis' bisa ditemukan
        $finder = $this->app['view']->getFinder();
        $hints = $finder->getHints();

        $this->assertArrayHasKey('tabel-dinamis', $hints);
    }

    // ─── Gate 'kelola-tabel-dinamis' ─────────────────────────

    public function test_gate_true_untuk_email_admin(): void
    {
        // Set ADMIN_EMAIL di env
        $this->app['env'] = 'testing';
        putenv('ADMIN_EMAIL=admin@tabeldinamis.test');

        $admin = User::factory()->create([
            'email' => 'admin@tabeldinamis.test',
        ]);

        $this->assertTrue(
            Gate::forUser($admin)->allows('kelola-tabel-dinamis'),
            'Gate harus bernilai true jika email user = ADMIN_EMAIL.'
        );
    }

    public function test_gate_false_untuk_email_bukan_admin(): void
    {
        putenv('ADMIN_EMAIL=admin@tabeldinamis.test');

        $bukan = User::factory()->create([
            'email' => 'pengguna@lain.test',
        ]);

        $this->assertFalse(
            Gate::forUser($bukan)->allows('kelola-tabel-dinamis'),
            'Gate harus bernilai false jika email user bukan ADMIN_EMAIL.'
        );
    }

    public function test_gate_false_jika_admin_email_tidak_diset(): void
    {
        putenv('ADMIN_EMAIL');

        $user = User::factory()->create([
            'email' => 'siapapun@test.test',
        ]);

        $this->assertFalse(
            Gate::forUser($user)->allows('kelola-tabel-dinamis'),
            'Gate harus bernilai false jika ADMIN_EMAIL tidak diset di .env.'
        );
    }

    // ─── Rute dimuat dalam grup web + prefix ─────────────────

    public function test_routes_file_dimuat_oleh_provider(): void
    {
        // File routes/tabel_dinamis.php harus ada dan bisa dibaca
        $this->assertFileExists(base_path('routes/tabel_dinamis.php'));
    }
}


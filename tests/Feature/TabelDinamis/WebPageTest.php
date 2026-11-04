<?php

namespace Tests\Feature\TabelDinamis;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes Halaman Web Frontend Tabel Dinamis (Blade).
 */
class WebPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_index_menampilkan_komponen_filter(): void
    {
        $response = $this->get('/tabel-dinamis');

        $response->assertOk();
        $response->assertSee('Tabel Dinamis Indikator Bidang IPW');
        $response->assertSee('Filter Tabel Dinamis');
        $response->assertSee('td-select-subjek', false);
        $response->assertSee('td-select-indikator', false);
        $response->assertSee('td-btn-tambah', false);
        $response->assertSee('td-btn-submit', false);
        $response->assertSee('td-tabel-hasil', false);
        $response->assertSee('meta name="csrf-token"', false);
    }
}


<?php

namespace Tests\Feature\TabelDinamis;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tamu dapat membuka halaman beranda KINERA dengan sukses (200 OK).
     */
    public function test_tamu_dapat_mengakses_halaman_beranda(): void
    {
        $response = $this->get('/tabel-dinamis/beranda');

        $response->assertOk();
        $response->assertSee('Satu Pintu Eksplorasi');
        $response->assertSee('Mengapa KINERA Dibangun?');
        $response->assertSee('Fitur Unggulan KINERA');
        $response->assertSee('Alur Pengolahan Data dalam 4 Langkah');
    }

    /**
     * Pintasan /beranda dialihkan ke rute tabel-dinamis.beranda.
     */
    public function test_pintasan_beranda_mengalihkan_ke_halaman_beranda(): void
    {
        $response = $this->get('/beranda');

        $response->assertRedirect(route('tabel-dinamis.beranda'));
    }
}

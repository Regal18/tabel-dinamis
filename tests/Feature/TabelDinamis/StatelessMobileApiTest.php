<?php

namespace Tests\Feature\TabelDinamis;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes Feature FASE W7: Rute Stateless API untuk Klien Mobile.
 * Memverifikasi setiap endpoint stateless merespons identik dengan versi web.
 */
class StatelessMobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected Opd $opd;
    protected Subjek $subjek;
    protected Wilayah $wilayah1;
    protected Wilayah $wilayah2;
    protected Indikator $indikator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->opd = Opd::create(['nama' => 'Diskominfo Malut']);
        $this->subjek = Subjek::create(['nama' => 'Komunikasi dan Informatika']);
        $this->wilayah1 = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->wilayah2 = Wilayah::create(['nama' => 'Kota Tidore Kepulauan']);

        $this->indikator = Indikator::create([
            'kode' => 'MOB-01',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indeks SPBE',
            'sub_indikator' => 'Domain Kebijakan',
            'satuan' => 'Indeks',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah1->id,
            'tahun' => 2023,
            'jenis' => 'Realisasi',
            'nilai' => 3.75,
            'sumber' => 'BAPPEDA',
        ]);
    }

    public function test_stateless_subjek_identik_dengan_versi_web(): void
    {
        $resWeb = $this->getJson('/tabel-dinamis/api/subjek');
        $resMobile = $this->getJson('/api/subjek');

        $resWeb->assertOk();
        $resMobile->assertOk();
        $this->assertEquals($resWeb->json(), $resMobile->json());
    }

    public function test_stateless_indikator_identik_dengan_versi_web(): void
    {
        $urlWeb = "/tabel-dinamis/api/indikator?subjek_id={$this->subjek->id}&q=kebijakan";
        $urlMobile = "/api/indikator?subjek_id={$this->subjek->id}&q=kebijakan";

        $resWeb = $this->getJson($urlWeb);
        $resMobile = $this->getJson($urlMobile);

        $resWeb->assertOk();
        $resMobile->assertOk();
        $this->assertEquals($resWeb->json(), $resMobile->json());
    }

    public function test_stateless_tahun_identik_dengan_versi_web(): void
    {
        $query = "?indikator_id={$this->indikator->id}&jenis=Realisasi";
        $resWeb = $this->getJson("/tabel-dinamis/api/tahun{$query}");
        $resMobile = $this->getJson("/api/tahun{$query}");

        $resWeb->assertOk();
        $resMobile->assertOk();
        $this->assertEquals($resWeb->json(), $resMobile->json());
        $this->assertEquals([2023], $resMobile->json());
    }

    public function test_stateless_wilayah_identik_dengan_versi_web(): void
    {
        $resWeb = $this->getJson('/tabel-dinamis/api/wilayah');
        $resMobile = $this->getJson('/api/wilayah');

        $resWeb->assertOk();
        $resMobile->assertOk();
        $this->assertEquals($resWeb->json(), $resMobile->json());
    }

    public function test_stateless_tabel_identik_dengan_versi_web_tanpa_csrf(): void
    {
        $payload = [
            'wilayah_ids' => [$this->wilayah1->id, $this->wilayah2->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2023],
                ],
            ],
        ];

        // Web (menggunakan rute tabel-dinamis/api/tabel)
        $resWeb = $this->postJson('/tabel-dinamis/api/tabel', $payload);
        $resWeb->assertOk();

        // Mobile Stateless (menggunakan rute /api/tabel tanpa cookie session / token CSRF)
        $resMobile = $this->postJson('/api/tabel', $payload);
        $resMobile->assertOk();

        // Balasan JSON harus identik persis
        $this->assertEquals($resWeb->json(), $resMobile->json());
        $this->assertEquals(3.75, $resMobile->json()['baris'][0]['nilai'][0][0]);
        $this->assertNull($resMobile->json()['baris'][1]['nilai'][0][0]);
    }

    public function test_stateless_export_xlsx_identik_dengan_versi_web(): void
    {
        $payload = [
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2023],
                ],
            ],
        ];

        $resWeb = $this->post('/tabel-dinamis/api/export?format=xlsx', $payload);
        $resMobile = $this->post('/api/export?format=xlsx', $payload);

        $resWeb->assertOk();
        $resMobile->assertOk();
        $resMobile->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_stateless_endpoint_juga_dapat_diakses_dengan_sub_prefix(): void
    {
        // Mendukung /api/tabel-dinamis/*
        $response = $this->getJson('/api/tabel-dinamis/subjek');
        $response->assertOk();
        $response->assertJsonCount(1);
    }
}

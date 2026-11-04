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
 * Tes Feature untuk Endpoint POST /api/export (XLSX dan CSV).
 */
class ExportApiTest extends TestCase
{
    use RefreshDatabase;

    protected Indikator $indikator;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        $opd = Opd::create(['nama' => 'Diskominfo']);
        $subjek = Subjek::create(['nama' => 'Kominfo']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'EXP-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Export',
            'satuan' => 'Persen',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 90.5,
        ]);
    }

    public function test_export_xlsx_berhasil(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/export?format=xlsx', [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition'));
    }

    public function test_export_csv_berhasil(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/export?format=csv', [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }
}


<?php

namespace Tests\Feature\TabelDinamis;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Tes Feature FASE W5: Endpoint POST /api/export (XLSX dan CSV).
 */
class ExportApiTest extends TestCase
{
    use RefreshDatabase;

    protected Indikator $indikator;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        $opd = Opd::create(['nama' => 'Diskominfo Malut']);
        $subjek = Subjek::create(['nama' => 'Kominfo']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'EXP-W5-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Export W5',
            'satuan' => 'Persen',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 92.5,
        ]);
    }

    public function test_export_xlsx_berhasil_dan_isi_sama_dengan_tabel(): void
    {
        $payload = [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ];

        // 1. Ambil data dari endpoint /api/tabel
        $tabelRes = $this->postJson('/tabel-dinamis/api/tabel', $payload);
        $tabelRes->assertOk();
        $tabelData = $tabelRes->json();

        $nilaiTabel = $tabelData['baris'][0]['nilai'][0][0]; // 92.5
        $wilayahTabel = $tabelData['baris'][0]['wilayah']; // Kota Ternate

        // 2. Ambil berkas dari endpoint /api/export?format=xlsx
        $exportRes = $this->post('/tabel-dinamis/api/export?format=xlsx', $payload);
        $exportRes->assertOk();
        $exportRes->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Simpan output ke file sementara dan verifikasi dengan PhpSpreadsheet
        $tempPath = tempnam(sys_get_temp_dir(), 'td_tst_') . '.xlsx';
        file_put_contents($tempPath, $exportRes->streamedContent());

        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();

        // Verifikasi isi file sama persis dengan hasil /tabel
        $this->assertEquals($wilayahTabel, $sheet->getCell('A3')->getValue());
        $this->assertEquals($nilaiTabel, (float)$sheet->getCell('B3')->getValue());
        // Verifikasi freeze pane
        $this->assertEquals('B3', $sheet->getFreezePane());
        // Verifikasi catatan keterangan di bawah tabel
        $this->assertStringContainsString('Catatan:', $sheet->getCell('A5')->getValue());

        @unlink($tempPath);
    }

    public function test_export_csv_berhasil_memakai_titik_koma_dan_utf8_bom(): void
    {
        $payload = [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ];

        $response = $this->post('/tabel-dinamis/api/export?format=csv', $payload);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Verifikasi UTF-8 BOM (\xEF\xBB\xBF)
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // Verifikasi pemisah titik koma (;) dan isi baris
        $this->assertStringContainsString('"Kabupaten / Kota";', $content);
        $this->assertStringContainsString('"Kota Ternate";', $content);
        $this->assertStringContainsString('92.5', $content);
    }

    public function test_format_tak_dikenal_mengembalikan_422(): void
    {
        $payload = [
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ];

        // Format 'pdf' tidak dikenal
        $response = $this->postJson('/tabel-dinamis/api/export?format=pdf', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('format');

        // Tanpa format juga ditolak 422
        $responseTanpaFormat = $this->postJson('/tabel-dinamis/api/export', $payload);
        $responseTanpaFormat->assertStatus(422);
        $responseTanpaFormat->assertJsonValidationErrors('format');
    }
}

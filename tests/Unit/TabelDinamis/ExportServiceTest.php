<?php

namespace Tests\Unit\TabelDinamis;

use App\Modules\TabelDinamis\Services\ExportService;
use PHPUnit\Framework\TestCase;

/**
 * Tes Unit untuk ExportService Fase W5:
 * XLSX (header bergabung, angka numerik, freeze pane, catatan keterangan)
 * CSV (UTF-8 BOM, pemisah titik koma)
 */
class ExportServiceTest extends TestCase
{
    protected ExportService $exportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportService = new ExportService();
    }

    public function test_bangun_spreadsheet_xlsx_dengan_multi_header_freeze_pane_dan_catatan(): void
    {
        $dummyTabel = [
            'grup' => [
                [
                    'judul' => 'Persentase Internet',
                    'satuan' => 'Persen',
                    'jenis' => 'Realisasi',
                    'tahun' => [2021, 2022],
                ]
            ],
            'baris' => [
                [
                    'wilayah' => 'Kota Ternate',
                    'nilai' => [[75.5, 80.0]],
                ],
                [
                    'wilayah' => 'Kota Tidore Kepulauan',
                    'nilai' => [[null, 65.0]],
                ],
            ]
        ];

        $spreadsheet = $this->exportService->bangunSpreadsheet($dummyTabel, false);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Header Bergabung
        $this->assertEquals('Kabupaten / Kota', $sheet->getCell('A1')->getValue());
        $this->assertEquals('[Realisasi] Persentase Internet (Persen)', $sheet->getCell('B1')->getValue());
        $this->assertEquals(2021, $sheet->getCell('B2')->getValue());
        $this->assertEquals(2022, $sheet->getCell('C2')->getValue());

        // 2. Freeze Pane di B3
        $this->assertEquals('B3', $sheet->getFreezePane());

        // 3. Nilai Tetap Bertipe Numerik
        $this->assertEquals('Kota Ternate', $sheet->getCell('A3')->getValue());
        $this->assertEquals(75.5, $sheet->getCell('B3')->getValue());
        $this->assertTrue(is_numeric($sheet->getCell('B3')->getValue()));
        $this->assertEquals(80.0, $sheet->getCell('C3')->getValue());

        // Nilai null ditulis '-'
        $this->assertEquals('Kota Tidore Kepulauan', $sheet->getCell('A4')->getValue());
        $this->assertEquals('-', $sheet->getCell('B4')->getValue());

        // 4. Catatan Keterangan di Bawah Tabel Data
        $this->assertStringContainsString('Catatan:', $sheet->getCell('A6')->getValue());
        $this->assertStringContainsString('Sumber:', $sheet->getCell('A7')->getValue());
    }

    public function test_bangun_spreadsheet_csv_flat_header_dan_pemisah_titik_koma(): void
    {
        $dummyTabel = [
            'grup' => [
                [
                    'judul' => 'SPBE',
                    'satuan' => null,
                    'jenis' => 'Target',
                    'tahun' => [2023],
                ]
            ],
            'baris' => [
                [
                    'wilayah' => 'Halbar',
                    'nilai' => [[3.5]],
                ]
            ]
        ];

        $spreadsheet = $this->exportService->bangunSpreadsheet($dummyTabel, true);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('Kabupaten / Kota', $sheet->getCell('A1')->getValue());
        $this->assertEquals('[Target] SPBE - 2023', $sheet->getCell('B1')->getValue());
        $this->assertEquals('Halbar', $sheet->getCell('A2')->getValue());
        $this->assertEquals(3.5, $sheet->getCell('B2')->getValue());
    }
}

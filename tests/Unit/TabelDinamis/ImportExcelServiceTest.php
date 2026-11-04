<?php

namespace Tests\Unit\TabelDinamis;

use App\Modules\TabelDinamis\Exceptions\ImportGagalException;
use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use App\Modules\TabelDinamis\Services\ImportExcelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Tes Unit untuk ImportExcelService menerapkan Pseudocode 2A.
 */
class ImportExcelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImportExcelService $service;
    protected Indikator $indikator;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImportExcelService();

        $opd = Opd::create(['nama' => 'Diskominfo Malut']);
        $subjek = Subjek::create(['nama' => 'Kominfo']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'IMP-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Impor Unit',
            'satuan' => 'Unit',
        ]);
    }

    /**
     * Helper membuat berkas Excel (.xlsx) sementara untuk pengujian.
     */
    protected function buatXlsxTemp(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'td_imp_') . '.xlsx';
        $writer->save($tempPath);

        return $tempPath;
    }

    public function test_file_valid_masuk_ke_database(): void
    {
        $file = $this->buatXlsxTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', 2023, 'Realisasi', 75.25, 'BPS Malut'],
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertTrue($hasil['sukses']);
        $this->assertEquals(1, $hasil['total']);
        $this->assertDatabaseHas('td_data_indikator', [
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2023,
            'jenis' => 'Realisasi',
            'nilai' => 75.2500,
            'sumber' => 'BPS Malut',
        ]);
    }

    public function test_file_dengan_1_baris_salah_tidak_ada_data_yang_masuk(): void
    {
        $file = $this->buatXlsxTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', 2022, 'Realisasi', 50.0, 'Data Baris 2 Benar'],
            ['KODE-TIDAK-VALID', 'Kota Ternate', 2022, 'Realisasi', 60.0, 'Data Baris 3 Salah'],
        ]);

        try {
            $this->service->import($file);
            $this->fail('Harus melempar ImportGagalException jika ada baris salah.');
        } catch (ImportGagalException $e) {
            $galat = $e->getDaftarGalat();
            $this->assertNotEmpty($galat);
            $this->assertStringContainsString('Baris 3', $galat[0]);
            $this->assertStringContainsString('KODE-TIDAK-VALID', $galat[0]);
        } finally {
            @unlink($file);
        }

        // Atomik: Pastikan tidak ada data yang tersimpan sama sekali di td_data_indikator
        $this->assertEquals(0, DataIndikator::count());
    }

    public function test_impor_dua_kali_tidak_dobel_idempoten(): void
    {
        $file1 = $this->buatXlsxTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', 2024, 'Target', 80.0, 'Rencana Awal'],
        ]);
        $this->service->import($file1);
        @unlink($file1);

        $this->assertEquals(1, DataIndikator::count());

        // Impor kedua kali data yang sama dengan pembaruan nilai
        $file2 = $this->buatXlsxTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', 2024, 'Target', 85.0, 'Rencana Revisi'],
        ]);
        $this->service->import($file2);
        @unlink($file2);

        // Jumlah baris tetap 1 (tidak duplikat)
        $this->assertEquals(1, DataIndikator::count());
        $this->assertDatabaseHas('td_data_indikator', [
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2024,
            'jenis' => 'Target',
            'nilai' => 85.0000,
            'sumber' => 'Rencana Revisi',
        ]);
    }

    public function test_validasi_tahun_dan_jenis_dan_wilayah_menyebut_nomor_baris(): void
    {
        $file = $this->buatXlsxTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['IMP-01', 'Kota Ternate', 2019, 'Estimasi', 99.0], // Baris 2: tahun < 2021 & jenis salah
        ]);

        try {
            $this->service->import($file);
            $this->fail('Harus melempar ImportGagalException.');
        } catch (ImportGagalException $e) {
            $galat = $e->getDaftarGalat();
            $this->assertStringContainsString('Baris 2', $galat[0]);
        } finally {
            @unlink($file);
        }
    }
}

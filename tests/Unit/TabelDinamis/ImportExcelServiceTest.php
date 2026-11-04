<?php

namespace Tests\Unit\TabelDinamis;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use App\Modules\TabelDinamis\Services\ImportExcelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes Unit untuk ImportExcelService.
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

        $opd = Opd::create(['nama' => 'Diskominfo']);
        $subjek = Subjek::create(['nama' => 'Kominfo']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'IMP-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Impor',
            'satuan' => 'Unit',
        ]);
    }

    protected function buatCsvTemp(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'td_test_') . '.csv';
        $fp = fopen($path, 'w');
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        return $path;
    }

    public function test_impor_berhasil_dan_idempoten(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', '2023', 'Realisasi', '75.2', 'BPS'],
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
            'nilai' => 75.2000,
        ]);

        // Ulangi dengan nilai berbeda untuk uji idempoten (update)
        $file2 = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', '2023', 'Realisasi', '80.0', 'Update'],
        ]);

        $hasil2 = $this->service->import($file2);
        @unlink($file2);

        $this->assertTrue($hasil2['sukses']);
        $this->assertEquals(1, DataIndikator::count());
        $this->assertDatabaseHas('td_data_indikator', [
            'nilai' => 80.0000,
            'sumber' => 'Update',
        ]);
    }

    public function test_impor_atomik_rollback_jika_ada_baris_salah(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['IMP-01', 'Kota Ternate', '2022', 'Realisasi', '50', 'Sumber A'], // Baris 2: Benar
            ['IMP-TIDAK-ADA', 'Kota Ternate', '2022', 'Realisasi', '60', 'Sumber B'], // Baris 3: Salah kode
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('Baris 3', $hasil['errors'][0]);
        // Pastikan tidak ada data yang tersimpan sama sekali (Atomik)
        $this->assertEquals(0, DataIndikator::count());
    }

    public function test_validasi_tahun_diluar_rentang(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['IMP-01', 'Kota Ternate', '2020', 'Realisasi', '50'], // Tahun 2020 di luar 2021-2026
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('Baris 2', $hasil['errors'][0]);
        $this->assertStringContainsString('2020', $hasil['errors'][0]);
    }

    public function test_validasi_jenis_hanya_realisasi_atau_target(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['IMP-01', 'Kota Ternate', '2023', 'Prakiraan', '50'],
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('Baris 2', $hasil['errors'][0]);
        $this->assertStringContainsString('Prakiraan', $hasil['errors'][0]);
    }

    public function test_validasi_wilayah_tidak_dikenal(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['IMP-01', 'Kota Atlantis', '2023', 'Realisasi', '50'],
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('Kota Atlantis', $hasil['errors'][0]);
    }

    public function test_impor_nilai_null_diterima(): void
    {
        $file = $this->buatCsvTemp([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['IMP-01', 'Kota Ternate', '2024', 'Realisasi', ''], // Nilai kosong
        ]);

        $hasil = $this->service->import($file);
        @unlink($file);

        $this->assertTrue($hasil['sukses']);
        $this->assertDatabaseHas('td_data_indikator', [
            'tahun' => 2024,
            'nilai' => null,
        ]);
    }
}


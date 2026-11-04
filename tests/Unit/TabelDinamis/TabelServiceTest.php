<?php

namespace Tests\Unit\TabelDinamis;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use App\Modules\TabelDinamis\Services\TabelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes unit untuk TabelService — logika bisnis inti.
 */
class TabelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TabelService $service;
    protected Opd $opd;
    protected Subjek $subjek;
    protected Wilayah $wilayah1;
    protected Wilayah $wilayah2;
    protected Indikator $indikator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TabelService();

        // Siapkan data tes
        $this->opd = Opd::create(['nama' => 'OPD Tes']);
        $this->subjek = Subjek::create(['nama' => 'Subjek Tes']);
        $this->wilayah1 = Wilayah::create(['nama' => 'Wilayah A']);
        $this->wilayah2 = Wilayah::create(['nama' => 'Wilayah B']);
        $this->indikator = Indikator::create([
            'kode' => 'TES-001',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indikator Tes',
            'satuan' => 'Persen',
        ]);

        // Isi data
        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah1->id,
            'tahun' => 2021,
            'jenis' => 'Realisasi',
            'nilai' => 50.25,
        ]);
        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah1->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 60.00,
        ]);
        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah2->id,
            'tahun' => 2021,
            'jenis' => 'Realisasi',
            'nilai' => null, // Belum ada data
        ]);
    }

    public function test_bangun_tabel_satu_grup(): void
    {
        $hasil = $this->service->bangunTabel(
            wilayahIds: null,
            dataPermintaan: [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2021, 2022],
                ],
            ]
        );

        // Harus ada 1 grup
        $this->assertCount(1, $hasil['grup']);
        $this->assertEquals('Indikator Tes', $hasil['grup'][0]['judul']);
        $this->assertEquals('Persen', $hasil['grup'][0]['satuan']);
        $this->assertEquals('Realisasi', $hasil['grup'][0]['jenis']);
        $this->assertEquals([2021, 2022], $hasil['grup'][0]['tahun']);

        // Harus ada 2 baris (Wilayah A dan B, urut nama)
        $this->assertCount(2, $hasil['baris']);
        $this->assertEquals('Wilayah A', $hasil['baris'][0]['wilayah']);
        $this->assertEquals('Wilayah B', $hasil['baris'][1]['wilayah']);

        // Nilai Wilayah A: [50.25, 60.00]
        $this->assertEquals(50.25, $hasil['baris'][0]['nilai'][0][0]);
        $this->assertEquals(60.00, $hasil['baris'][0]['nilai'][0][1]);

        // Nilai Wilayah B: [null, null] — 2021 ada tapi null, 2022 tidak ada
        $this->assertNull($hasil['baris'][1]['nilai'][0][0]); // null eksplisit
        $this->assertNull($hasil['baris'][1]['nilai'][0][1]); // tidak ada data
    }

    public function test_bangun_tabel_filter_wilayah(): void
    {
        $hasil = $this->service->bangunTabel(
            wilayahIds: [$this->wilayah1->id],
            dataPermintaan: [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2021],
                ],
            ]
        );

        $this->assertCount(1, $hasil['baris']);
        $this->assertEquals('Wilayah A', $hasil['baris'][0]['wilayah']);
        $this->assertEquals(50.25, $hasil['baris'][0]['nilai'][0][0]);
    }

    public function test_bangun_tabel_dua_grup(): void
    {
        $indikator2 = Indikator::create([
            'kode' => 'TES-002',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indikator Dua',
            'satuan' => 'Unit',
        ]);

        DataIndikator::create([
            'indikator_id' => $indikator2->id,
            'wilayah_id' => $this->wilayah1->id,
            'tahun' => 2023,
            'jenis' => 'Target',
            'nilai' => 100.00,
        ]);

        $hasil = $this->service->bangunTabel(
            wilayahIds: [$this->wilayah1->id],
            dataPermintaan: [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2021],
                ],
                [
                    'indikator_id' => $indikator2->id,
                    'jenis' => 'Target',
                    'tahun' => [2023],
                ],
            ]
        );

        $this->assertCount(2, $hasil['grup']);
        $this->assertCount(1, $hasil['baris']);

        // nilai[0] = grup 1, nilai[1] = grup 2
        $this->assertEquals(50.25, $hasil['baris'][0]['nilai'][0][0]);
        $this->assertEquals(100.00, $hasil['baris'][0]['nilai'][1][0]);
    }

    public function test_tahun_diurutkan_dalam_grup(): void
    {
        $hasil = $this->service->bangunTabel(
            wilayahIds: null,
            dataPermintaan: [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022, 2021], // sengaja terbalik
                ],
            ]
        );

        // Tahun harus terurut
        $this->assertEquals([2021, 2022], $hasil['grup'][0]['tahun']);
    }

    public function test_label_indikator_dengan_sub_indikator(): void
    {
        $ind = Indikator::create([
            'kode' => 'TES-SUB-001',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indikator Utama',
            'sub_indikator' => 'Sub Bagian A',
            'satuan' => 'Indeks',
        ]);

        $this->assertEquals('Indikator Utama — Sub Bagian A', $ind->label);
    }

    public function test_label_indikator_tanpa_sub_indikator(): void
    {
        $this->assertEquals('Indikator Tes', $this->indikator->label);
    }
}


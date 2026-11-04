<?php

namespace Tests\Unit\TabelDinamis;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes unit untuk model dan relasi.
 */
class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_opd_memiliki_banyak_indikator(): void
    {
        $opd = Opd::create(['nama' => 'OPD Model Tes']);
        $subjek = Subjek::create(['nama' => 'Subjek Model Tes']);

        Indikator::create([
            'kode' => 'MDL-001',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Model',
        ]);

        $this->assertCount(1, $opd->indikator);
    }

    public function test_subjek_memiliki_banyak_indikator(): void
    {
        $opd = Opd::create(['nama' => 'OPD']);
        $subjek = Subjek::create(['nama' => 'Subjek']);

        Indikator::create([
            'kode' => 'MDL-002',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Ind 1',
        ]);
        Indikator::create([
            'kode' => 'MDL-003',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Ind 2',
        ]);

        $this->assertCount(2, $subjek->indikator);
    }

    public function test_indikator_milik_opd_dan_subjek(): void
    {
        $opd = Opd::create(['nama' => 'OPD Relasi']);
        $subjek = Subjek::create(['nama' => 'Subjek Relasi']);

        $indikator = Indikator::create([
            'kode' => 'MDL-004',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Ind Relasi',
        ]);

        $this->assertEquals($opd->id, $indikator->opd->id);
        $this->assertEquals($subjek->id, $indikator->subjek->id);
    }

    public function test_data_indikator_constraint_unik(): void
    {
        $opd = Opd::create(['nama' => 'OPD Unik']);
        $subjek = Subjek::create(['nama' => 'Subjek Unik']);
        $wilayah = Wilayah::create(['nama' => 'Wil Unik']);

        $indikator = Indikator::create([
            'kode' => 'MDL-005',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Ind Unik',
        ]);

        DataIndikator::create([
            'indikator_id' => $indikator->id,
            'wilayah_id' => $wilayah->id,
            'tahun' => 2021,
            'jenis' => 'Realisasi',
            'nilai' => 10,
        ]);

        // Duplikat harus gagal
        $this->expectException(\Illuminate\Database\QueryException::class);

        DataIndikator::create([
            'indikator_id' => $indikator->id,
            'wilayah_id' => $wilayah->id,
            'tahun' => 2021,
            'jenis' => 'Realisasi',
            'nilai' => 20,
        ]);
    }

    public function test_wilayah_memiliki_banyak_data_indikator(): void
    {
        $opd = Opd::create(['nama' => 'O']);
        $subjek = Subjek::create(['nama' => 'S']);
        $wilayah = Wilayah::create(['nama' => 'W']);

        $ind = Indikator::create([
            'kode' => 'MDL-006',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'I',
        ]);

        DataIndikator::create([
            'indikator_id' => $ind->id,
            'wilayah_id' => $wilayah->id,
            'tahun' => 2021,
            'jenis' => 'Realisasi',
            'nilai' => 1,
        ]);
        DataIndikator::create([
            'indikator_id' => $ind->id,
            'wilayah_id' => $wilayah->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 2,
        ]);

        $this->assertCount(2, $wilayah->dataIndikator);
    }
}


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
 * Pengujian Fitur FASE W3: Halaman Web Tampilan Filter dan Tabel Dinamis.
 */
class WebPageTest extends TestCase
{
    use RefreshDatabase;

    protected Indikator $indikator1;
    protected Indikator $indikator2;
    protected Indikator $indikator3;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        $opd = Opd::create(['nama' => 'Diskominfo Malut']);
        $subjek = Subjek::create(['nama' => 'Kominfo']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);

        $this->indikator1 = Indikator::create([
            'kode' => 'W3-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Web 1',
        ]);
        $this->indikator2 = Indikator::create([
            'kode' => 'W3-02',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Web 2',
        ]);
        $this->indikator3 = Indikator::create([
            'kode' => 'W3-03',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indikator Web 3',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator1->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 85.0,
        ]);
    }

    public function test_halaman_menampilkan_seluruh_komponen_alur_1b(): void
    {
        $response = $this->get('/tabel-dinamis');

        $response->assertOk();

        // 1. Banner 'Hanya dapat memilih maksimal 2 data'
        $response->assertSee('Hanya dapat memilih maksimal 2 data');

        // 2. Dropdown Subjek
        $response->assertSee('id="td-subjek"', false);

        // 3. Pencarian & Daftar Indikator (debounce)
        $response->assertSee('id="td-cari-indikator"', false);
        $response->assertSee('id="td-indikator"', false);

        // 4. Jenis Data
        $response->assertSee('id="td-jenis"', false);
        $response->assertSee('Realisasi');
        $response->assertSee('Target');

        // 5. Centang Tahun & Wilayah
        $response->assertSee('id="td-tahun-list"', false);
        $response->assertSee('id="td-semua-wilayah"', false);
        $response->assertSee('id="td-wilayah-list"', false);

        // 6. Tombol Tambah, Atur Ulang, Submit
        $response->assertSee('id="td-btn-tambah"', false);
        $response->assertSee('id="td-btn-reset"', false);
        $response->assertSee('id="td-btn-submit"', false);

        // 7. Kontainer Data Terpilih (bisa dihapus)
        $response->assertSee('id="td-data-terpilih"', false);

        // 8. Tabel Hasil yang bisa digeser horizontal
        $response->assertSee('td-table-scroll', false);
        $response->assertSee('id="td-table-head"', false);
        $response->assertSee('id="td-table-body"', false);

        // 9. Meta CSRF Token
        $response->assertSee('name="csrf-token"', false);

        // 10. Alamat API JS dibangun dari prefix config (tidak di-hardcode)
        $response->assertSee('ROUTE_PREFIX', false);
        $response->assertSee('API_BASE', false);
    }

    public function test_server_menerima_1_sampai_2_data(): void
    {
        // 1 data valid
        $res1 = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
            ]
        ]);
        $res1->assertOk();

        // 2 data valid
        $res2 = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator2->id, 'jenis' => 'Target', 'tahun' => [2023]],
            ]
        ]);
        $res2->assertOk();
    }

    public function test_server_menolak_lebih_dari_2_data_dengan_status_422(): void
    {
        // 3 data ditolak di sisi server
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator2->id, 'jenis' => 'Target', 'tahun' => [2023]],
                ['indikator_id' => $this->indikator3->id, 'jenis' => 'Realisasi', 'tahun' => [2024]],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('data');
    }
}

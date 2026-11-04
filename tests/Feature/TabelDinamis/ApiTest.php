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
 * Tes fitur API referensi dan tabel — endpoint HTTP.
 */
class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected Opd $opd;
    protected Subjek $subjek;
    protected Wilayah $wilayah;
    protected Indikator $indikator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->opd = Opd::create(['nama' => 'OPD Tes']);
        $this->subjek = Subjek::create(['nama' => 'Subjek Tes']);
        $this->wilayah = Wilayah::create(['nama' => 'Wilayah Tes']);
        $this->indikator = Indikator::create([
            'kode' => 'API-001',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indikator API Tes',
            'satuan' => 'Persen',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 88.5,
            'sumber' => 'Tes',
        ]);
    }

    public function test_api_subjek(): void
    {
        $response = $this->getJson('/tabel-dinamis/api/subjek');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['nama' => 'Subjek Tes']);
    }

    public function test_api_indikator_dengan_subjek_id(): void
    {
        $response = $this->getJson("/tabel-dinamis/api/indikator?subjek_id={$this->subjek->id}");

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'kode' => 'API-001',
                'label' => 'Indikator API Tes',
                'satuan' => 'Persen',
                'opd' => 'OPD Tes',
            ]);
    }

    public function test_api_indikator_tanpa_subjek_id_gagal(): void
    {
        $response = $this->getJson('/tabel-dinamis/api/indikator');

        $response->assertStatus(422);
    }

    public function test_api_indikator_dengan_pencarian_q(): void
    {
        Indikator::create([
            'kode' => 'API-002',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indikator Lain Khusus',
            'sub_indikator' => 'Sub Bagian Spesifik',
            'satuan' => 'Unit',
        ]);

        // Uji pencarian case-insensitive huruf kecil vs besar
        $response = $this->getJson("/tabel-dinamis/api/indikator?subjek_id={$this->subjek->id}&q=api");
        $response->assertOk()->assertJsonCount(2);

        $response2 = $this->getJson("/tabel-dinamis/api/indikator?subjek_id={$this->subjek->id}&q=KHUSUS");
        $response2->assertOk()->assertJsonCount(1)->assertJsonFragment(['kode' => 'API-002']);

        $response3 = $this->getJson("/tabel-dinamis/api/indikator?subjek_id={$this->subjek->id}&q=spesifik");
        $response3->assertOk()->assertJsonCount(1)->assertJsonFragment(['kode' => 'API-002']);
    }

    public function test_bentuk_json_persis_sesuai_kontrak(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'grup' => [
                    '*' => [
                        'judul',
                        'satuan',
                        'jenis',
                        'tahun',
                    ]
                ],
                'baris' => [
                    '*' => [
                        'wilayah',
                        'nilai',
                    ]
                ],
            ]);

        $json = $response->json();
        $this->assertIsArray($json['grup']);
        $this->assertIsArray($json['baris']);
        $this->assertIsArray($json['baris'][0]['nilai']);
        $this->assertIsArray($json['baris'][0]['nilai'][0]);
        // Format nilai[i][j]
        $this->assertEquals(88.5, $json['baris'][0]['nilai'][0][0]);
    }

    public function test_api_tahun(): void
    {
        $response = $this->getJson(
            "/tabel-dinamis/api/tahun?indikator_id={$this->indikator->id}&jenis=Realisasi"
        );

        $response->assertOk()
            ->assertExactJson([2022]);
    }

    public function test_api_tahun_tanpa_parameter_gagal(): void
    {
        $response = $this->getJson('/tabel-dinamis/api/tahun');

        $response->assertStatus(422);
    }

    public function test_api_wilayah(): void
    {
        $response = $this->getJson('/tabel-dinamis/api/wilayah');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['nama' => 'Wilayah Tes']);
    }

    public function test_api_tabel_satu_data(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'wilayah_ids' => [$this->wilayah->id],
            'data' => [
                [
                    'indikator_id' => $this->indikator->id,
                    'jenis' => 'Realisasi',
                    'tahun' => [2022],
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'grup' => [['judul', 'satuan', 'jenis', 'tahun']],
                'baris' => [['wilayah', 'nilai']],
            ]);

        $data = $response->json();
        $this->assertEquals('Indikator API Tes', $data['grup'][0]['judul']);
        $this->assertEquals(88.5, $data['baris'][0]['nilai'][0][0]);
    }

    public function test_api_tabel_validasi_maks_2_data(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Target', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Realisasi', 'tahun' => [2021]],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_api_tabel_validasi_jenis_invalid(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Anggaran', 'tahun' => [2022]],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_api_tabel_validasi_tahun_diluar_rentang(): void
    {
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Realisasi', 'tahun' => [2020]],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_halaman_index_terbuka(): void
    {
        $response = $this->get('/tabel-dinamis');

        $response->assertOk();
    }
}


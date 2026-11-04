<?php

namespace Tests\Feature\TabelDinamis;

use App\Models\User;
use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Uji Penerimaan Kriteria SKPL Bagian 3.4 (Tabel Dinamis Indikator Bidang IPW)
 */
class SkplAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Opd $opd;
    protected Subjek $subjek;
    protected Wilayah $wilayah1;
    protected Wilayah $wilayah2;
    protected Indikator $indikator1;
    protected Indikator $indikator2;
    protected Indikator $indikator3;

    protected function setUp(): void
    {
        parent::setUp();

        putenv('ADMIN_EMAIL=admin@tabeldinamis.test');

        $this->adminUser = User::factory()->create([
            'email' => 'admin@tabeldinamis.test',
        ]);
        $this->regularUser = User::factory()->create([
            'email' => 'staff@bappeda.test',
        ]);

        $this->opd = Opd::create(['nama' => 'Dinas Komunikasi dan Informatika (Diskominfo)']);
        $this->subjek = Subjek::create(['nama' => 'Komunikasi dan Informatika']);
        $this->wilayah1 = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->wilayah2 = Wilayah::create(['nama' => 'Kota Tidore Kepulauan']);

        $this->indikator1 = Indikator::create([
            'kode' => 'SKPL-01',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Persentase Desa Terjangkau Internet',
            'satuan' => 'Persen',
        ]);
        $this->indikator2 = Indikator::create([
            'kode' => 'SKPL-02',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Indeks SPBE',
            'sub_indikator' => 'Domain Layanan',
            'satuan' => 'Indeks',
        ]);
        $this->indikator3 = Indikator::create([
            'kode' => 'SKPL-03',
            'opd_id' => $this->opd->id,
            'subjek_id' => $this->subjek->id,
            'nama' => 'Konektivitas Wilayah',
            'satuan' => 'Rasio',
        ]);

        // Isi data indikator 1 untuk Ternate
        DataIndikator::create([
            'indikator_id' => $this->indikator1->id,
            'wilayah_id' => $this->wilayah1->id,
            'tahun' => 2022,
            'jenis' => 'Realisasi',
            'nilai' => 82.50,
        ]);
    }

    /**
     * Kriteria 1: Filter tahun hanya mengizinkan rentang 2021-2026.
     */
    public function test_skpl_3_4_kriteria_1_tahun_hanya_2021_sampai_2026(): void
    {
        // Tahun 2020 ditolak
        $resBawah = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2020]],
            ]
        ]);
        $resBawah->assertStatus(422);

        // Tahun 2027 ditolak
        $resAtas = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2027]],
            ]
        ]);
        $resAtas->assertStatus(422);

        // Tahun 2021-2026 diterima
        $resValid = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2021, 2026]],
            ]
        ]);
        $resValid->assertOk();
    }

    /**
     * Kriteria 2: Maksimal 2 data per tabel hasil (validasi server).
     */
    public function test_skpl_3_4_kriteria_2_maksimal_2_data_per_tabel(): void
    {
        // 2 data diterima
        $res2 = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator2->id, 'jenis' => 'Target', 'tahun' => [2023]],
            ]
        ]);
        $res2->assertOk();

        // 3 data ditolak
        $res3 = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
                ['indikator_id' => $this->indikator2->id, 'jenis' => 'Target', 'tahun' => [2023]],
                ['indikator_id' => $this->indikator3->id, 'jenis' => 'Realisasi', 'tahun' => [2024]],
            ]
        ]);
        $res3->assertStatus(422);
    }

    /**
     * Kriteria 3: Jenis data hanya diizinkan 'Realisasi' atau 'Target'.
     */
    public function test_skpl_3_4_kriteria_3_jenis_hanya_realisasi_atau_target(): void
    {
        // Jenis 'Estimasi' ditolak
        $resInvalid = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Estimasi', 'tahun' => [2022]],
            ]
        ]);
        $resInvalid->assertStatus(422);

        // Jenis 'Realisasi' diterima
        $resRealisasi = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
            ]
        ]);
        $resRealisasi->assertOk();

        // Jenis 'Target' diterima
        $resTarget = $this->postJson('/tabel-dinamis/api/tabel', [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Target', 'tahun' => [2022]],
            ]
        ]);
        $resTarget->assertOk();
    }

    /**
     * Kriteria 4: Nilai null dihasilkan jika data belum ada (klien menampilkan '-').
     */
    public function test_skpl_3_4_kriteria_4_nilai_null_untuk_data_kosong(): void
    {
        // Wilayah Tidore belum punya data untuk tahun 2022
        $response = $this->postJson('/tabel-dinamis/api/tabel', [
            'wilayah_ids' => [$this->wilayah2->id],
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
            ]
        ]);

        $response->assertOk();
        $json = $response->json();
        // Nilai harus null
        $this->assertNull($json['baris'][0]['nilai'][0][0]);
    }

    /**
     * Kriteria 5: Impor Excel atomik dan idempoten, dengan laporan galat menyebut nomor baris.
     */
    public function test_skpl_3_4_kriteria_5_impor_atomik_dan_idempoten(): void
    {
        // 5a. Berkas valid
        $file = $this->buatXlsxFile([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['SKPL-01', 'Kota Ternate', 2024, 'Realisasi', 95.0, 'BPS'],
        ]);

        $resSukses = $this->actingAs($this->adminUser)->post('/tabel-dinamis/admin/impor', ['file' => $file]);
        $resSukses->assertSessionHas('success');
        $this->assertEquals(1, DataIndikator::where('tahun', 2024)->count());

        // 5b. Idempoten: impor ulang tidak dobel
        $resUlang = $this->actingAs($this->adminUser)->post('/tabel-dinamis/admin/impor', ['file' => $file]);
        $resUlang->assertSessionHas('success');
        $this->assertEquals(1, DataIndikator::where('tahun', 2024)->count());

        // 5c. Atomik: 1 baris salah menggugurkan seluruh baris
        $fileSalah = $this->buatXlsxFile([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['SKPL-01', 'Kota Ternate', 2025, 'Realisasi', 90.0], // Benar
            ['KODE-PALSU', 'Kota Ternate', 2025, 'Realisasi', 80.0], // Salah di baris 3
        ]);

        $resSalah = $this->actingAs($this->adminUser)->post('/tabel-dinamis/admin/impor', ['file' => $fileSalah]);
        $resSalah->assertSessionHas('error');
        $resSalah->assertSessionHas('import_errors');

        // Pastikan baris 2025 tidak masuk (rollback)
        $this->assertEquals(0, DataIndikator::where('tahun', 2025)->count());
    }

    /**
     * Kriteria 6: Hanya Gate 'kelola-tabel-dinamis' yang boleh mengakses impor.
     */
    public function test_skpl_3_4_kriteria_6_proteksi_gate_kelola_tabel_dinamis(): void
    {
        // Tamu -> 401
        $this->get('/tabel-dinamis/admin/impor')->assertStatus(401);

        // Non-admin -> 403
        $this->actingAs($this->regularUser)->get('/tabel-dinamis/admin/impor')->assertStatus(403);

        // Admin -> 200
        $this->actingAs($this->adminUser)->get('/tabel-dinamis/admin/impor')->assertOk();
    }

    /**
     * Kriteria 7: Export Excel dan CSV sesuai kontrak dan struktur data.
     */
    public function test_skpl_3_4_kriteria_7_export_xlsx_dan_csv_sesuai(): void
    {
        $payload = [
            'data' => [
                ['indikator_id' => $this->indikator1->id, 'jenis' => 'Realisasi', 'tahun' => [2022]],
            ]
        ];

        // XLSX
        $resXlsx = $this->post('/tabel-dinamis/api/export?format=xlsx', $payload);
        $resXlsx->assertOk();
        $resXlsx->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // CSV
        $resCsv = $this->post('/tabel-dinamis/api/export?format=csv', $payload);
        $resCsv->assertOk();
        $resCsv->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringStartsWith("\xEF\xBB\xBF", $resCsv->streamedContent());
    }

    protected function buatXlsxFile(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'skpl_') . '.xlsx';
        $writer->save($tempPath);

        return new UploadedFile(
            $tempPath,
            'skpl_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}


<?php

namespace Tests\Feature\TabelDinamis;

use App\Models\User;
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
 * Tes Fitur FASE W4: Halaman Admin Impor Excel dan Pengamanan Otorisasi.
 */
class AdminImporTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected string $adminEmail = 'admin@tabeldinamis.test';
    protected Indikator $indikator;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        putenv("ADMIN_EMAIL={$this->adminEmail}");

        $this->adminUser = User::factory()->create([
            'email' => $this->adminEmail,
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'staff@bappeda.test',
        ]);

        $opd = Opd::create(['nama' => 'DLH Malut']);
        $subjek = Subjek::create(['nama' => 'Lingkungan Hidup']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'DLH-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indeks Kualitas Lingkungan',
        ]);
    }

    /**
     * Helper membuat UploadedFile bertipe XLSX nyata.
     */
    protected function buatUploadedXlsx(array $rows, string $namaFile = 'data_uji.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'td_upl_') . '.xlsx';
        $writer->save($tempPath);

        return new UploadedFile(
            $tempPath,
            $namaFile,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    public function test_tanpa_kredensial_mengembalikan_401(): void
    {
        $response = $this->get('/tabel-dinamis/admin/impor');
        $response->assertStatus(401);

        $postRes = $this->post('/tabel-dinamis/admin/impor', []);
        $postRes->assertStatus(401);
    }

    public function test_user_non_admin_mengembalikan_403(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/tabel-dinamis/admin/impor');
        $response->assertStatus(403);

        $postRes = $this->actingAs($this->regularUser)->post('/tabel-dinamis/admin/impor', []);
        $postRes->assertStatus(403);
    }

    public function test_user_admin_diizinkan_200(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/tabel-dinamis/admin/impor');
        $response->assertOk();
        $response->assertSee('Impor Data Indikator (Excel)');
    }

    public function test_upload_file_valid_masuk_ke_database(): void
    {
        $file = $this->buatUploadedXlsx([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['DLH-01', 'Kota Ternate', 2023, 'Realisasi', 88.50, 'DLH Malut'],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('td_data_indikator', [
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2023,
            'jenis' => 'Realisasi',
            'nilai' => 88.5000,
        ]);
    }

    public function test_file_dengan_1_baris_salah_menampilkan_laporan_galat(): void
    {
        $file = $this->buatUploadedXlsx([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai'],
            ['KODE-TIDAK-ADA', 'Kota Ternate', 2023, 'Realisasi', 50.0],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('import_errors');
    }

    public function test_file_bukan_xlsx_ditolak_validasi(): void
    {
        // Berkas teks bukan xlsx
        $file = UploadedFile::fake()->create('dokumen.txt', 100, 'text/plain');

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_file_lebih_dari_5_mb_ditolak_validasi(): void
    {
        // Berkas xlsx berukuran 6000 KB (> 5120 KB / 5 MB)
        $fileBesar = UploadedFile::fake()->create('raksasa.xlsx', 6000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $fileBesar,
            ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_unduh_template_excel(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/tabel-dinamis/admin/impor/template');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('template_impor_indikator_ipw.xlsx', $response->headers->get('Content-Disposition'));
    }
}

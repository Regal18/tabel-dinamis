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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Tes Integrasi & Smoke Test Mode Taskboard (FASE W8).
 *
 * Mensimulasikan kondisi modul saat digabungkan ke aplikasi Taskboard:
 * - middleware_baca = ['auth']
 * - middleware_admin = ['auth', 'can:kelola-tabel-dinamis']
 * - Gate 'kelola-tabel-dinamis' berbasis role taskboard (bukan email .env)
 * - Pelepasan auth.basic
 */
class TaskboardIntegrationSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $staffUser;
    protected Indikator $indikator;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Simulasikan titik integrasi 1 & 2: middleware mode taskboard
        config(['tabel_dinamis.middleware_baca' => ['auth']]);
        config(['tabel_dinamis.middleware_admin' => ['auth', 'can:kelola-tabel-dinamis']]);

        // Simulasikan rute login yang dimiliki aplikasi taskboard induk
        Route::get('/login', fn() => 'Halaman Login Taskboard')->name('login');

        // Muat ulang rute agar membaca config baru
        Route::middleware('web')
            ->prefix(config('tabel_dinamis.prefix', 'tabel-dinamis'))
            ->group(base_path('routes/tabel_dinamis.php'));

        // 2. Simulasikan titik integrasi 3: Gate berbasis atribut/role taskboard
        Gate::define('kelola-tabel-dinamis', function ($user) {
            // Pengguna dengan name/role admin diizinkan
            return str_contains(strtolower($user->name), 'admin');
        });

        // 3. Buat akun pengguna simulasi taskboard
        $this->adminUser = User::factory()->create([
            'name' => 'Admin Taskboard BAPPEDA',
            'email' => 'admin_taskboard@malutprov.go.id',
        ]);

        $this->staffUser = User::factory()->create([
            'name' => 'Staf OPD IPW',
            'email' => 'staf_opd@malutprov.go.id',
        ]);

        // 4. Data referensi
        $opd = Opd::create(['nama' => 'Diskominfo Maluku Utara']);
        $subjek = Subjek::create(['nama' => 'Infrastruktur']);
        $this->wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        $this->indikator = Indikator::create([
            'kode' => 'TB-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Tingkat Penetrasi Internet',
            'satuan' => 'Persen',
        ]);

        DataIndikator::create([
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2023,
            'jenis' => 'Realisasi',
            'nilai' => 86.4,
            'sumber' => 'Diskominfo',
        ]);
    }

    /**
     * Smoke Test 1: Tamu (belum login) mengakses halaman baca -> ditolak/redirect.
     */
    public function test_smoke_1_tamu_belum_login_dialihkan(): void
    {
        $response = $this->get('/tabel-dinamis');
        // Middleware 'auth' mengalihkan tamu ke route login (status 302)
        $response->assertStatus(302);
    }

    /**
     * Smoke Test 2: Pengguna taskboard yang sudah login dapat membuka halaman filter.
     */
    public function test_smoke_2_pengguna_login_bisa_buka_filter(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/tabel-dinamis');
        $response->assertOk();
        $response->assertSee('Filter Tabel Dinamis');
    }

    /**
     * Smoke Test 3: Pengguna login melakukan submit kueri tabel dinamis.
     */
    public function test_smoke_3_pengguna_login_submit_tabel_berhasil(): void
    {
        $response = $this->actingAs($this->staffUser)
            ->postJson('/tabel-dinamis/api/tabel', [
                'wilayah_ids' => [$this->wilayah->id],
                'data' => [
                    [
                        'indikator_id' => $this->indikator->id,
                        'jenis' => 'Realisasi',
                        'tahun' => [2023],
                    ],
                ],
            ]);

        $response->assertOk();
        $this->assertEquals(86.4, $response->json()['baris'][0]['nilai'][0][0]);
    }

    /**
     * Smoke Test 4: Pengguna login mengunduh berkas ekspor XLSX dan CSV.
     */
    public function test_smoke_4_pengguna_login_export_berkas_berhasil(): void
    {
        $payload = [
            'data' => [
                ['indikator_id' => $this->indikator->id, 'jenis' => 'Realisasi', 'tahun' => [2023]],
            ],
        ];

        // Ekspor XLSX
        $resXlsx = $this->actingAs($this->staffUser)->post('/tabel-dinamis/api/export?format=xlsx', $payload);
        $resXlsx->assertOk();
        $resXlsx->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Ekspor CSV
        $resCsv = $this->actingAs($this->staffUser)->post('/tabel-dinamis/api/export?format=csv', $payload);
        $resCsv->assertOk();
        $resCsv->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Smoke Test 5: Pengguna non-admin mengakses impor admin -> ditolak 403 Forbidden.
     */
    public function test_smoke_5_pengguna_non_admin_ditolak_403_saat_akses_impor(): void
    {
        // Staf biasa ditolak Gate 'kelola-tabel-dinamis'
        $response = $this->actingAs($this->staffUser)->get('/tabel-dinamis/admin/impor');
        $response->assertStatus(403);
    }

    /**
     * Smoke Test 6: Admin taskboard dapat membuka halaman impor dan mengunggah berkas.
     */
    public function test_smoke_6_admin_taskboard_bisa_impor_berkas(): void
    {
        // Admin membuka halaman form impor
        $resForm = $this->actingAs($this->adminUser)->get('/tabel-dinamis/admin/impor');
        $resForm->assertOk();

        // Admin mengunggah berkas Excel valid
        $file = $this->buatXlsxFile([
            ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'],
            ['TB-01', 'Kota Ternate', 2024, 'Target', 90.0, 'Rencana Taskboard'],
        ]);

        $resUpload = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $resUpload->assertSessionHas('success');
        $this->assertDatabaseHas('td_data_indikator', [
            'indikator_id' => $this->indikator->id,
            'wilayah_id' => $this->wilayah->id,
            'tahun' => 2024,
            'jenis' => 'Target',
            'nilai' => 90.0000,
        ]);
    }

    protected function buatXlsxFile(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'tb_') . '.xlsx';
        $writer->save($tempPath);

        return new UploadedFile(
            $tempPath,
            'taskboard_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}

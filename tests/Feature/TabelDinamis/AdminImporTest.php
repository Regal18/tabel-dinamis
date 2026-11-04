<?php

namespace Tests\Feature\TabelDinamis;

use App\Models\User;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Tes Feature Halaman Admin Impor Excel dan Pengamanan Gate / Auth.
 */
class AdminImporTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected string $adminEmail = 'admin@tabeldinamis.test';

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
    }

    public function test_akses_admin_tanpa_login_ditolak_401(): void
    {
        $response = $this->get('/tabel-dinamis/admin/impor');
        $response->assertStatus(401);
    }

    public function test_akses_admin_bukan_admin_ditolak_403(): void
    {
        // Login sebagai user biasa, lolos auth tapi dicekal Gate 'kelola-tabel-dinamis'
        $response = $this->actingAs($this->regularUser)->get('/tabel-dinamis/admin/impor');
        $response->assertStatus(403);
    }

    public function test_akses_admin_oleh_email_admin_diizinkan_200(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/tabel-dinamis/admin/impor');
        $response->assertOk();
        $response->assertSee('Impor Data Indikator (Excel / CSV)');
    }

    public function test_upload_excel_oleh_admin_berhasil(): void
    {
        $opd = Opd::create(['nama' => 'DLH']);
        $subjek = Subjek::create(['nama' => 'Lingkungan']);
        $wilayah = Wilayah::create(['nama' => 'Kota Ternate']);
        Indikator::create([
            'kode' => 'DLH-01',
            'opd_id' => $opd->id,
            'subjek_id' => $subjek->id,
            'nama' => 'Indeks Kualitas Udara',
        ]);

        $csvContent = "kode_indikator,nama_wilayah,tahun,jenis,nilai,sumber\nDLH-01,Kota Ternate,2023,Realisasi,85.5,DLH Malut\n";
        $file = UploadedFile::fake()->createWithContent('data.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('td_data_indikator', [
            'tahun' => 2023,
            'nilai' => 85.5000,
        ]);
    }

    public function test_upload_excel_dengan_kesalahan_menampilkan_pesan_error(): void
    {
        $csvContent = "kode_indikator,nama_wilayah,tahun,jenis,nilai\nKODE-SALAH,Kota Ternate,2023,Realisasi,10\n";
        $file = UploadedFile::fake()->createWithContent('salah.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/impor', [
                'file' => $file,
            ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('import_errors');
    }
}


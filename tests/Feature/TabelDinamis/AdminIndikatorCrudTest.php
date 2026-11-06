<?php

namespace Tests\Feature\TabelDinamis;

use App\Models\User;
use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian Manajemen Master Data Indikator (CRUD) oleh Admin.
 */
class AdminIndikatorCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected string $adminEmail = 'admin@tabeldinamis.test';
    protected Subjek $subjek;
    protected Opd $opd;

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

        $this->subjek = Subjek::create(['nama' => 'Infrastruktur Wilayah']);
        $this->opd = Opd::create(['nama' => 'Dinas PUPR Malut']);
    }

    public function test_guest_tidak_bisa_mengakses_crud_indikator(): void
    {
        $response = $this->get('/tabel-dinamis/admin/indikator');
        $response->assertStatus(401);

        $postRes = $this->post('/tabel-dinamis/admin/indikator', []);
        $postRes->assertStatus(401);
    }

    public function test_user_non_admin_ditolak_403(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/tabel-dinamis/admin/indikator');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_melihat_daftar_indikator(): void
    {
        Indikator::create([
            'kode' => 'PUPR-01',
            'subjek_id' => $this->subjek->id,
            'opd_id' => $this->opd->id,
            'nama' => 'Kemantapan Jalan Provinsi',
            'satuan' => '%',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/tabel-dinamis/admin/indikator');
        $response->assertOk();
        $response->assertSee('Kemantapan Jalan Provinsi');
        $response->assertSee('PUPR-01');
    }

    public function test_admin_bisa_menambah_indikator_baru(): void
    {
        $payload = [
            'kode' => 'PUPR-02',
            'subjek_id' => $this->subjek->id,
            'opd_id' => $this->opd->id,
            'nama' => 'Akses Sanitasi Layak',
            'sub_indikator' => 'Perkotaan',
            'satuan' => '%',
            'keterangan' => 'Definisi sanitasi sehat',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/indikator', $payload);

        $response->assertRedirect('/tabel-dinamis/admin/indikator');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('td_indikator', [
            'kode' => 'PUPR-02',
            'nama' => 'Akses Sanitasi Layak',
            'sub_indikator' => 'Perkotaan',
        ]);
    }

    public function test_tambah_indikator_kode_duplikat_ditolak_validasi(): void
    {
        Indikator::create([
            'kode' => 'PUPR-01',
            'subjek_id' => $this->subjek->id,
            'opd_id' => $this->opd->id,
            'nama' => 'Indikator Lama',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post('/tabel-dinamis/admin/indikator', [
                'kode' => 'PUPR-01',
                'subjek_id' => $this->subjek->id,
                'opd_id' => $this->opd->id,
                'nama' => 'Indikator Baru Duplikat',
            ]);

        $response->assertSessionHasErrors(['kode']);
    }

    public function test_admin_bisa_memperbarui_indikator(): void
    {
        $indikator = Indikator::create([
            'kode' => 'PUPR-01',
            'subjek_id' => $this->subjek->id,
            'opd_id' => $this->opd->id,
            'nama' => 'Nama Sebelum Update',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put("/tabel-dinamis/admin/indikator/{$indikator->id}", [
                'kode' => 'PUPR-01',
                'subjek_id' => $this->subjek->id,
                'opd_id' => $this->opd->id,
                'nama' => 'Nama Sesudah Update',
                'satuan' => 'Km',
            ]);

        $response->assertRedirect('/tabel-dinamis/admin/indikator');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('td_indikator', [
            'id' => $indikator->id,
            'nama' => 'Nama Sesudah Update',
            'satuan' => 'Km',
        ]);
    }

    public function test_admin_bisa_menghapus_indikator_beserta_datanya(): void
    {
        $wilayah = Wilayah::create(['nama' => 'Kabupaten Halmahera Barat']);
        $indikator = Indikator::create([
            'kode' => 'PUPR-99',
            'subjek_id' => $this->subjek->id,
            'opd_id' => $this->opd->id,
            'nama' => 'Indikator Yang Akan Dihapus',
        ]);

        DataIndikator::create([
            'indikator_id' => $indikator->id,
            'wilayah_id' => $wilayah->id,
            'tahun' => 2023,
            'jenis' => 'Realisasi',
            'nilai' => 75.5,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete("/tabel-dinamis/admin/indikator/{$indikator->id}");

        $response->assertRedirect('/tabel-dinamis/admin/indikator');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('td_indikator', ['id' => $indikator->id]);
        $this->assertDatabaseMissing('td_data_indikator', ['indikator_id' => $indikator->id]);
    }
}


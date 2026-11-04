<?php

namespace Database\Seeders;

use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\DataIndikator;
use Illuminate\Database\Seeder;

/**
 * Seeder data awal modul Tabel Dinamis.
 *
 * CATATAN:
 * - Nama wilayah PERLU DIVERIFIKASI DENGAN BARIS TABEL BPS.
 * - Data indikator dan nilai bertanda 'CONTOH' — bukan data riil.
 * - Akun admin dibuat dari env (ADMIN_EMAIL, ADMIN_PASSWORD).
 */
class TabelDinamisSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedOpd();
        $this->seedSubjek();
        $this->seedWilayah();
        $this->seedContohIndikator();
        $this->seedContohData();
    }

    /**
     * Buat akun admin dari .env (jangan hardcode kredensial).
     */
    protected function seedAdmin(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL atau ADMIN_PASSWORD belum diisi di .env. Lewati pembuatan akun admin.');

            return;
        }

        \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin Tabel Dinamis',
                'password' => bcrypt($password),
            ]
        );

        $this->command?->info("Akun admin dibuat/diperbarui: {$email}");
    }

    /**
     * Seed 8 OPD bidang IPW.
     */
    protected function seedOpd(): void
    {
        $daftar = [
            'Dinas Komunikasi dan Informatika (Diskominfo)',
            'Dinas Perhubungan (Dishub)',
            'Dinas Pariwisata',
            'Dinas Lingkungan Hidup (DLH)',
            'Badan Pengelola Perbatasan Daerah (BPPD)',
            'Dinas Perumahan dan Kawasan Permukiman (Perkim)',
            'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)',
            'Dinas Energi dan Sumber Daya Mineral (DESDM)',
        ];

        foreach ($daftar as $nama) {
            Opd::firstOrCreate(['nama' => $nama]);
        }
    }

    /**
     * Seed subjek contoh — CONTOH, sesuaikan dengan data riil.
     */
    protected function seedSubjek(): void
    {
        $daftar = [
            'Infrastruktur',
            'Transportasi',
            'Pariwisata',
            'Lingkungan Hidup',
            'Komunikasi dan Informatika',
            'Energi dan Sumber Daya Mineral',
            'Perumahan dan Permukiman',
            'Perbatasan',
        ];

        foreach ($daftar as $nama) {
            Subjek::firstOrCreate(['nama' => $nama]);
        }
    }

    /**
     * Seed wilayah kabupaten/kota Maluku Utara.
     * PERLU DIVERIFIKASI DENGAN BARIS TABEL BPS.
     */
    protected function seedWilayah(): void
    {
        $daftar = [
            'Kab. Halmahera Barat',
            'Kab. Halmahera Tengah',
            'Kab. Halmahera Utara',
            'Kab. Halmahera Selatan',
            'Kab. Halmahera Timur',
            'Kab. Kepulauan Sula',
            'Kab. Pulau Morotai',
            'Kab. Pulau Taliabu',
            'Kota Ternate',
            'Kota Tidore Kepulauan',
        ];

        foreach ($daftar as $nama) {
            Wilayah::firstOrCreate(['nama' => $nama]);
        }
    }

    /**
     * Seed indikator contoh — CONTOH, bukan data riil.
     */
    protected function seedContohIndikator(): void
    {
        $diskominfo = Opd::where('nama', 'like', '%Diskominfo%')->first();
        $dishub = Opd::where('nama', 'like', '%Dishub%')->first();
        $subjekInfra = Subjek::where('nama', 'Infrastruktur')->first();
        $subjekTransportasi = Subjek::where('nama', 'Transportasi')->first();
        $subjekKominfo = Subjek::where('nama', 'Komunikasi dan Informatika')->first();

        if (! $diskominfo || ! $dishub || ! $subjekInfra || ! $subjekTransportasi || ! $subjekKominfo) {
            return;
        }

        // CONTOH — bukan data riil
        Indikator::firstOrCreate(
            ['kode' => 'CONTOH-KOMINFO-01'],
            [
                'opd_id' => $diskominfo->id,
                'subjek_id' => $subjekKominfo->id,
                'nama' => '[CONTOH] Persentase Desa/Kelurahan yang Terjangkau Jaringan Internet',
                'sub_indikator' => null,
                'satuan' => 'Persen',
                'keterangan' => 'Data CONTOH — bukan data riil.',
            ]
        );

        Indikator::firstOrCreate(
            ['kode' => 'CONTOH-KOMINFO-02'],
            [
                'opd_id' => $diskominfo->id,
                'subjek_id' => $subjekKominfo->id,
                'nama' => '[CONTOH] Indeks SPBE',
                'sub_indikator' => 'Indeks Domain Kebijakan',
                'satuan' => 'Indeks',
                'keterangan' => 'Data CONTOH — bukan data riil.',
            ]
        );

        Indikator::firstOrCreate(
            ['kode' => 'CONTOH-DISHUB-01'],
            [
                'opd_id' => $dishub->id,
                'subjek_id' => $subjekTransportasi->id,
                'nama' => '[CONTOH] Rasio Konektivitas Antar Pulau',
                'sub_indikator' => null,
                'satuan' => 'Rasio',
                'keterangan' => 'Data CONTOH — bukan data riil.',
            ]
        );
    }

    /**
     * Seed data contoh — CONTOH, bukan data riil.
     */
    protected function seedContohData(): void
    {
        $indikator = Indikator::where('kode', 'CONTOH-KOMINFO-01')->first();
        $ternate = Wilayah::where('nama', 'Kota Ternate')->first();
        $tidore = Wilayah::where('nama', 'like', '%Tidore%')->first();

        if (! $indikator || ! $ternate || ! $tidore) {
            return;
        }

        // CONTOH data — bukan data riil
        $contohData = [
            ['wilayah_id' => $ternate->id, 'tahun' => 2021, 'jenis' => 'Realisasi', 'nilai' => 75.50],
            ['wilayah_id' => $ternate->id, 'tahun' => 2022, 'jenis' => 'Realisasi', 'nilai' => 78.20],
            ['wilayah_id' => $ternate->id, 'tahun' => 2023, 'jenis' => 'Realisasi', 'nilai' => 80.10],
            ['wilayah_id' => $ternate->id, 'tahun' => 2021, 'jenis' => 'Target', 'nilai' => 80.00],
            ['wilayah_id' => $ternate->id, 'tahun' => 2022, 'jenis' => 'Target', 'nilai' => 82.00],
            ['wilayah_id' => $tidore->id, 'tahun' => 2021, 'jenis' => 'Realisasi', 'nilai' => 60.00],
            ['wilayah_id' => $tidore->id, 'tahun' => 2022, 'jenis' => 'Realisasi', 'nilai' => 65.30],
            ['wilayah_id' => $tidore->id, 'tahun' => 2023, 'jenis' => 'Realisasi', 'nilai' => null],
        ];

        foreach ($contohData as $data) {
            DataIndikator::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'wilayah_id' => $data['wilayah_id'],
                    'tahun' => $data['tahun'],
                    'jenis' => $data['jenis'],
                ],
                [
                    'nilai' => $data['nilai'],
                    'sumber' => 'Seeder CONTOH',
                ]
            );
        }
    }
}


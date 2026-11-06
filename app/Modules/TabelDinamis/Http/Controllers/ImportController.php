<?php

namespace App\Modules\TabelDinamis\Http\Controllers;

use App\Modules\TabelDinamis\Exceptions\ImportGagalException;
use App\Modules\TabelDinamis\Models\RiwayatImpor;
use App\Modules\TabelDinamis\Services\ImportExcelService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Controller Admin untuk Impor Berkas Excel Indikator IPW.
 * Menggunakan middleware_admin dari konfigurasi.
 */
class ImportController extends Controller
{
    public function __construct(
        protected ImportExcelService $importService
    ) {}

    /**
     * Tampilkan formulir unggah berkas impor beserta riwayat audit trail.
     */
    public function form()
    {
        $riwayat = RiwayatImpor::latest()->take(10)->get();

        return view('tabel-dinamis::admin.impor', [
            'layout' => config('tabel_dinamis.layout'),
            'riwayat' => $riwayat,
        ]);
    }

    /**
     * Memproses berkas Excel yang diunggah.
     * Validasi: Hanya berkas berekstensi .xlsx dan ukuran maksimal 5 MB (5120 KB).
     * Mencatat riwayat audit trail impor ke tabel td_riwayat_impor.
     */
    public function proses(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:5120',
        ], [
            'file.required' => 'Pilih berkas Excel terlebih dahulu.',
            'file.mimes' => 'Berkas harus berupa format Excel (.xlsx).',
            'file.max' => 'Ukuran berkas tidak boleh melebihi 5 MB.',
        ]);

        $uploadedFile = $request->file('file');
        $namaBerkas = $uploadedFile->getClientOriginalName();
        $ukuranKb = (int) round($uploadedFile->getSize() / 1024);
        $userEmail = auth()->user()?->email ?? $request->header('PHP_AUTH_USER', 'Admin');

        try {
            $hasil = $this->importService->import($uploadedFile->getRealPath());

            // Catat log sukses ke Audit Trail
            RiwayatImpor::create([
                'nama_berkas' => $namaBerkas,
                'ukuran_kb' => $ukuranKb,
                'diunggah_oleh' => $userEmail,
                'total_baris' => $hasil['total'] ?? 0,
                'status' => 'SUKSES',
                'pesan' => $hasil['pesan'] ?? 'Impor berhasil.',
            ]);

            // Bersihkan cache referensi agar data selalu mutakhir
            Cache::forget('td_subjek_list');
            Cache::forget('td_wilayah_list');

            return back()->with('success', $hasil['pesan']);
        } catch (ImportGagalException $e) {
            // Catat log kegagalan ke Audit Trail
            RiwayatImpor::create([
                'nama_berkas' => $namaBerkas,
                'ukuran_kb' => $ukuranKb,
                'diunggah_oleh' => $userEmail,
                'total_baris' => 0,
                'status' => 'GAGAL',
                'pesan' => $e->getMessage(),
            ]);

            return back()->withInput()->with([
                'error' => $e->getMessage(),
                'import_errors' => $e->getDaftarGalat(),
            ]);
        }
    }

    /**
     * Mengunduh berkas template Excel standar impor data indikator IPW.
     */
    public function template()
    {
        return $this->importService->downloadTemplate();
    }
}


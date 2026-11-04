<?php

namespace App\Modules\TabelDinamis\Http\Controllers;

use App\Modules\TabelDinamis\Exceptions\ImportGagalException;
use App\Modules\TabelDinamis\Services\ImportExcelService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
     * Tampilkan formulir unggah berkas impor.
     */
    public function form()
    {
        return view('tabel-dinamis::admin.impor', [
            'layout' => config('tabel_dinamis.layout'),
        ]);
    }

    /**
     * Memproses berkas Excel yang diunggah.
     * Validasi: Hanya berkas berekstensi .xlsx dan ukuran maksimal 5 MB (5120 KB).
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

        try {
            $hasil = $this->importService->import($request->file('file')->getRealPath());

            return back()->with('success', $hasil['pesan']);
        } catch (ImportGagalException $e) {
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

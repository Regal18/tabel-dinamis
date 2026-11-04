<?php

namespace App\Modules\TabelDinamis\Http\Controllers;

use App\Modules\TabelDinamis\Services\ImportExcelService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller halaman admin impor Excel.
 * Logika bisnis didelegasikan penuh ke ImportExcelService.
 */
class ImporController extends Controller
{
    public function __construct(
        protected ImportExcelService $importService
    ) {}

    /**
     * Tampilkan form impor Excel.
     */
    public function form()
    {
        return view('tabel-dinamis::admin.impor', [
            'layout' => config('tabel_dinamis.layout'),
        ]);
    }

    /**
     * Proses file Excel yang diunggah.
     */
    public function proses(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ], [
            'file.required' => 'Pilih file spreadsheet terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $uploadedFile = $request->file('file');
        $hasil = $this->importService->import($uploadedFile->getRealPath());

        if (!$hasil['sukses']) {
            return back()->withInput()->with([
                'error' => $hasil['pesan'],
                'import_errors' => $hasil['errors'],
            ]);
        }

        return back()->with('success', $hasil['pesan']);
    }
}

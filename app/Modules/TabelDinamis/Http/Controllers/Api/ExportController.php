<?php

namespace App\Modules\TabelDinamis\Http\Controllers\Api;

use App\Modules\TabelDinamis\Http\Requests\TabelRequest;
use App\Modules\TabelDinamis\Services\ExportService;
use App\Modules\TabelDinamis\Services\TabelService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Controller API untuk Export Berkas XLSX dan CSV.
 * Menerapkan spesifikasi Fase W5:
 * - Memakai TabelService yang SAMA dengan halaman tabel
 * - Validasi format: jika format tak dikenal -> HTTP 422
 */
class ExportController extends Controller
{
    public function __construct(
        protected TabelService $tabelService,
        protected ExportService $exportService
    ) {}

    /**
     * POST /api/export?format=xlsx|csv
     * Body sama persis dengan POST /api/tabel (divalidasi oleh TabelRequest).
     */
    public function export(TabelRequest $request)
    {
        // Ambil format dari query parameter atau body
        $format = $request->query('format', $request->input('format'));

        // Validasi format: wajib ada dan hanya boleh 'xlsx' atau 'csv'
        $formatValidator = Validator::make(['format' => $format], [
            'format' => 'required|string|in:xlsx,csv',
        ], [
            'format.required' => 'Parameter format wajib diisi (pilih xlsx atau csv).',
            'format.in' => 'Format ekspor tidak dikenal. Hanya format xlsx atau csv yang diizinkan.',
        ]);

        if ($formatValidator->fails()) {
            throw new ValidationException($formatValidator);
        }

        // Bangun data tabel menggunakan TabelService yang SAMA
        $dataTabel = $this->tabelService->bangun(
            wilayahIds: $request->validated('wilayah_ids'),
            dataPermintaan: $request->validated('data'),
        );

        return $this->exportService->export($format, $dataTabel);
    }
}

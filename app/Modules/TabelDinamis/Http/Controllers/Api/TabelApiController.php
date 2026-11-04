<?php

namespace App\Modules\TabelDinamis\Http\Controllers\Api;

use App\Modules\TabelDinamis\Http\Requests\TabelRequest;
use App\Modules\TabelDinamis\Services\TabelService;
use App\Modules\TabelDinamis\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controller API untuk query tabel dan export.
 * Controller tipis — logika bisnis di TabelService dan ExportService.
 */
class TabelApiController extends Controller
{
    public function __construct(
        protected TabelService $tabelService,
        protected ExportService $exportService
    ) {}

    /**
     * POST /api/tabel
     * Body: {wilayah_ids: [int]|null, data: [{indikator_id, jenis, tahun: [int]}]}
     */
    public function tabel(TabelRequest $request): JsonResponse
    {
        $hasil = $this->tabelService->bangunTabel(
            wilayahIds: $request->validated('wilayah_ids'),
            dataPermintaan: $request->validated('data'),
        );

        return response()->json($hasil);
    }

    /**
     * POST /api/export
     * Parameter query: format=xlsx|csv (default: xlsx)
     * Body sama dengan POST /api/tabel
     */
    public function export(TabelRequest $request)
    {
        $format = $request->query('format', 'xlsx');
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        $hasil = $this->tabelService->bangunTabel(
            wilayahIds: $request->validated('wilayah_ids'),
            dataPermintaan: $request->validated('data'),
        );

        return $this->exportService->export($format, $hasil);
    }
}


<?php

namespace App\Modules\TabelDinamis\Http\Controllers\Api;

use App\Modules\TabelDinamis\Http\Requests\TabelRequest;
use App\Modules\TabelDinamis\Services\ExportService;
use App\Modules\TabelDinamis\Services\TabelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controller API untuk POST /tabel dan POST /export sesuai KONTRAK API.
 * Stateless & controller tipis — logika didelegasikan ke Service.
 */
class TabelController extends Controller
{
    public function __construct(
        protected TabelService $tabelService,
        protected ExportService $exportService
    ) {}

    /**
     * POST /tabel
     * Body: {wilayah_ids: [int] atau null, data: [{indikator_id, jenis, tahun: [int]}]}
     * Balasan 200: {grup: [{judul, satuan, jenis, tahun}], baris: [{wilayah, nilai}]}
     */
    public function tabel(TabelRequest $request): JsonResponse
    {
        $hasil = $this->tabelService->bangun(
            wilayahIds: $request->validated('wilayah_ids'),
            dataPermintaan: $request->validated('data'),
        );

        return response()->json($hasil);
    }

    /**
     * POST /export
     * Parameter format: xlsx atau csv
     * Body sama dengan /tabel
     */
    public function export(TabelRequest $request)
    {
        $format = $request->query('format', 'xlsx');
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        $hasil = $this->tabelService->bangun(
            wilayahIds: $request->validated('wilayah_ids'),
            dataPermintaan: $request->validated('data'),
        );

        return $this->exportService->export($format, $hasil);
    }
}


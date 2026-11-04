<?php

namespace App\Modules\TabelDinamis\Http\Controllers\Api;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller API Filter referensi (subjek, indikator, tahun, wilayah).
 * Stateless & controller tipis.
 */
class FilterController extends Controller
{
    /**
     * GET /subjek -> [{id, nama}]
     */
    public function subjek(): JsonResponse
    {
        $data = Subjek::orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($data);
    }

    /**
     * GET /indikator -> parameter subjek_id dan q
     * Balasan: [{id, kode, label, satuan, opd}]
     * Pencarian menggunakan kombinasi LOWER + LIKE agar netral di MySQL, PostgreSQL, dan SQLite.
     */
    public function indikator(Request $request): JsonResponse
    {
        $request->validate([
            'subjek_id' => 'required|integer|exists:td_subjek,id',
            'q' => 'nullable|string|max:200',
        ]);

        $query = Indikator::where('subjek_id', $request->subjek_id)
            ->with('opd:id,nama');

        if ($request->filled('q')) {
            $q = mb_strtolower(trim($request->q));
            $query->where(function ($qb) use ($q) {
                $qb->whereRaw('LOWER(nama) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(sub_indikator) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(kode) LIKE ?', ["%{$q}%"]);
            });
        }

        $data = $query->orderBy('nama')->get()->map(function (Indikator $ind) {
            return [
                'id' => $ind->id,
                'kode' => $ind->kode,
                'label' => $ind->label,
                'satuan' => $ind->satuan,
                'opd' => $ind->opd?->nama,
            ];
        });

        return response()->json($data->values());
    }

    /**
     * GET /tahun -> parameter indikator_id dan jenis
     * Balasan: [2021, 2022, ...]
     */
    public function tahun(Request $request): JsonResponse
    {
        $request->validate([
            'indikator_id' => 'required|integer|exists:td_indikator,id',
            'jenis' => 'required|string|in:Realisasi,Target',
        ]);

        $tahun = DataIndikator::where('indikator_id', $request->indikator_id)
            ->where('jenis', $request->jenis)
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun')
            ->values();

        return response()->json($tahun);
    }

    /**
     * GET /wilayah -> [{id, nama}]
     */
    public function wilayah(): JsonResponse
    {
        $data = Wilayah::orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($data);
    }
}


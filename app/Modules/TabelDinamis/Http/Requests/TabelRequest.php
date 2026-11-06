<?php

namespace App\Modules\TabelDinamis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi request POST /api/tabel dan POST /api/export.
 *
 * Aturan bisnis:
 * - Maksimal 2 data per permintaan (config: maks_data_per_tabel).
 * - Tahun hanya 2021-2026.
 * - Jenis hanya 'Realisasi' atau 'Target'.
 */
class TabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi diatur di middleware rute
    }

    public function rules(): array
    {
        $tahunMin = config('tabel_dinamis.tahun_min', 2021);
        $tahunMax = config('tabel_dinamis.tahun_max', 2026);
        $maksData = config('tabel_dinamis.maks_data_per_tabel', null);

        $rulesData = 'required|array|min:1';
        if ($maksData !== null && (int)$maksData > 0) {
            $rulesData .= "|max:{$maksData}";
        }

        return [
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'integer|exists:td_wilayah,id',

            'data' => $rulesData,
            'data.*.indikator_id' => 'required|integer|exists:td_indikator,id',
            'data.*.jenis' => 'required|string|in:Realisasi,Target',
            'data.*.tahun' => 'required|array|min:1',
            'data.*.tahun.*' => "integer|min:{$tahunMin}|max:{$tahunMax}",
        ];
    }

    public function messages(): array
    {
        $maksData = config('tabel_dinamis.maks_data_per_tabel', null);

        return [
            'data.max' => "Maksimal {$maksData} data per permintaan tabel.",
            'data.*.jenis.in' => 'Jenis harus "Realisasi" atau "Target".',
            'data.*.tahun.*.min' => 'Tahun minimal ' . config('tabel_dinamis.tahun_min', 2021) . '.',
            'data.*.tahun.*.max' => 'Tahun maksimal ' . config('tabel_dinamis.tahun_max', 2026) . '.',
        ];
    }
}


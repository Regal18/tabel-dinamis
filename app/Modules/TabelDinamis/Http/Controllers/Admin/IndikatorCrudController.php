<?php

namespace App\Modules\TabelDinamis\Http\Controllers\Admin;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Controller Admin untuk Manajemen Master Data Indikator IPW (CRUD).
 * Dilindungi oleh middleware_admin (auth + gate can:kelola-tabel-dinamis).
 */
class IndikatorCrudController extends Controller
{
    /**
     * Tampilkan daftar indikator dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $subjekList = Subjek::orderBy('nama')->get();
        $opdList = Opd::orderBy('nama')->get();

        $query = Indikator::with(['subjek', 'opd'])
            ->withCount('dataIndikator');

        if ($request->filled('subjek_id')) {
            $query->where('subjek_id', $request->subjek_id);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('q')) {
            $q = mb_strtolower(trim($request->q));
            $query->where(function ($qb) use ($q) {
                $qb->whereRaw('LOWER(nama) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(sub_indikator) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(kode) LIKE ?', ["%{$q}%"]);
            });
        }

        $indikatorList = $query->orderBy('kode')->paginate(15)->withQueryString();

        return view('tabel-dinamis::admin.indikator.index', [
            'layout' => config('tabel_dinamis.layout'),
            'indikatorList' => $indikatorList,
            'subjekList' => $subjekList,
            'opdList' => $opdList,
        ]);
    }

    /**
     * Simpan indikator baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:td_indikator,kode',
            'subjek_id' => 'required|integer|exists:td_subjek,id',
            'opd_id' => 'required|integer|exists:td_opd,id',
            'nama' => 'required|string|max:255',
            'sub_indikator' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode.required' => 'Kode indikator wajib diisi.',
            'kode.unique' => 'Kode indikator sudah digunakan.',
            'subjek_id.required' => 'Subjek wajib dipilih.',
            'opd_id.required' => 'OPD wajib dipilih.',
            'nama.required' => 'Nama indikator wajib diisi.',
        ]);

        Indikator::create($validated);

        // Bersihkan cache referensi
        $this->bersihkanCache();

        return redirect()
            ->route('tabel-dinamis.admin.indikator.index')
            ->with('success', "Indikator [{$validated['kode']}] berhasil ditambahkan.");
    }

    /**
     * Perbarui data indikator yang sudah ada.
     */
    public function update(Request $request, Indikator $indikator)
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('td_indikator', 'kode')->ignore($indikator->id),
            ],
            'subjek_id' => 'required|integer|exists:td_subjek,id',
            'opd_id' => 'required|integer|exists:td_opd,id',
            'nama' => 'required|string|max:255',
            'sub_indikator' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode.required' => 'Kode indikator wajib diisi.',
            'kode.unique' => 'Kode indikator sudah digunakan oleh indikator lain.',
            'subjek_id.required' => 'Subjek wajib dipilih.',
            'opd_id.required' => 'OPD wajib dipilih.',
            'nama.required' => 'Nama indikator wajib diisi.',
        ]);

        $indikator->update($validated);

        // Bersihkan cache referensi
        $this->bersihkanCache();

        return redirect()
            ->route('tabel-dinamis.admin.indikator.index')
            ->with('success', "Indikator [{$indikator->kode}] berhasil diperbarui.");
    }

    /**
     * Hapus indikator dan data terkait (secara aman).
     */
    public function destroy(Request $request, Indikator $indikator)
    {
        $kode = $indikator->kode;
        $totalData = $indikator->dataIndikator()->count();

        DB::transaction(function () use ($indikator) {
            $indikator->dataIndikator()->delete();
            $indikator->delete();
        });

        // Bersihkan cache referensi
        $this->bersihkanCache();

        $pesan = "Indikator [{$kode}] berhasil dihapus.";
        if ($totalData > 0) {
            $pesan .= " ({$totalData} baris data terkait turut dibersihkan).";
        }

        return redirect()
            ->route('tabel-dinamis.admin.indikator.index')
            ->with('success', $pesan);
    }

    /**
     * Helper untuk menghapus cache referensi filter.
     */
    protected function bersihkanCache(): void
    {
        Cache::forget('td_subjek_list');
        Cache::forget('td_wilayah_list');
    }
}


<?php

namespace App\Modules\TabelDinamis\Http\Controllers;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Opd;
use App\Modules\TabelDinamis\Models\Subjek;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller halaman Beranda (Landing Page) KINERA.
 * Menampilkan ringkasan profil, tujuan sistem, fitur unggulan,
 * serta statistik data 8 OPD Bidang IPW BAPPEDA Maluku Utara.
 */
class BerandaController extends Controller
{
    /**
     * Tampilkan halaman Beranda KINERA.
     */
    public function index(Request $request)
    {
        $totalOpd = Opd::count();
        $totalSubjek = Subjek::count();
        $totalIndikator = Indikator::count();
        $totalWilayah = Wilayah::count();
        $totalData = DataIndikator::count();

        // Daftar OPD dengan jumlah indikator masing-masing
        $opdList = Opd::withCount('indikator')->orderBy('id')->get();

        // Daftar Subjek dengan jumlah indikator masing-masing
        $subjekList = Subjek::withCount('indikator')->orderBy('id')->get();

        return view('tabel-dinamis::beranda', [
            'layout' => config('tabel_dinamis.layout'),
            'totalOpd' => $totalOpd,
            'totalSubjek' => $totalSubjek,
            'totalIndikator' => $totalIndikator,
            'totalWilayah' => $totalWilayah,
            'totalData' => $totalData,
            'opdList' => $opdList,
            'subjekList' => $subjekList,
        ]);
    }
}


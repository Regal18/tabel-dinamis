<?php

namespace App\Modules\TabelDinamis\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller halaman utama Tabel Dinamis (Blade).
 * Logika bisnis didelegasikan ke TabelService.
 */
class TabelController extends Controller
{
    /**
     * Tampilkan halaman utama form filter + tabel hasil.
     */
    public function index(Request $request)
    {
        return view('tabel-dinamis::index', [
            'layout' => config('tabel_dinamis.layout'),
        ]);
    }
}


<?php

use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke modul Tabel Dinamis
Route::get('/', function () {
    return redirect()->route('tabel-dinamis.index');
});

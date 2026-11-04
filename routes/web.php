<?php

use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke modul Tabel Dinamis
Route::get('/', function () {
    return redirect()->route('tabel-dinamis.index');
});

// Placeholder rute login (digunakan oleh middleware auth saat integrasi taskboard)
Route::get('/login', function () {
    return 'Halaman Login Taskboard';
})->name('login');

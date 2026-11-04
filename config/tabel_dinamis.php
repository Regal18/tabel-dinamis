<?php

/**
 * Konfigurasi Modul Tabel Dinamis Indikator Bidang IPW.
 *
 * File ini adalah TITIK INTEGRASI utama saat modul digabungkan
 * ke aplikasi taskboard. Ubah nilai di sini sesuai kebutuhan.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Prefix Rute
    |--------------------------------------------------------------------------
    | Semua rute modul akan diawali prefix ini.
    | Default: 'tabel-dinamis' -> /tabel-dinamis, /tabel-dinamis/api/*
    */
    'prefix' => env('TD_ROUTE_PREFIX', 'tabel-dinamis'),

    /*
    |--------------------------------------------------------------------------
    | Middleware Baca (halaman publik & API baca)
    |--------------------------------------------------------------------------
    | Mode mandiri : [] (terbuka, untuk pengembangan lokal)
    | Mode taskboard: ['auth'] atau sesuai kebutuhan
    */
    'middleware_baca' => [],

    /*
    |--------------------------------------------------------------------------
    | Middleware Admin (impor Excel, kelola data)
    |--------------------------------------------------------------------------
    | Mode mandiri : ['auth.basic', 'can:kelola-tabel-dinamis']
    | Mode taskboard: ['auth', 'can:kelola-tabel-dinamis']
    */
    'middleware_admin' => ['auth.basic', 'can:kelola-tabel-dinamis'],

    /*
    |--------------------------------------------------------------------------
    | Layout Blade
    |--------------------------------------------------------------------------
    | Mode mandiri : 'tabel-dinamis::layouts.app' (layout bawaan modul)
    | Mode taskboard: 'layouts.app' atau layout taskboard lainnya
    */
    'layout' => 'tabel-dinamis::layout',

    /*
    |--------------------------------------------------------------------------
    | Tahun yang Tersedia
    |--------------------------------------------------------------------------
    | Rentang tahun data yang diizinkan sistem.
    */
    'tahun_min' => 2021,
    'tahun_max' => 2026,

    /*
    |--------------------------------------------------------------------------
    | Batas Maksimal Data per Permintaan Tabel
    |--------------------------------------------------------------------------
    | Jumlah maksimal item data (kombinasi indikator+jenis+tahun)
    | yang bisa diminta dalam satu permintaan tabel.
    */
    'maks_data_per_tabel' => 2,

    /*
    |--------------------------------------------------------------------------
    | Batas Maksimal Kesalahan Impor yang Dilaporkan
    |--------------------------------------------------------------------------
    */
    'maks_error_impor' => 100,

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (permintaan per menit)
    |--------------------------------------------------------------------------
    */
    'rate_limit_per_menit' => 60,

];


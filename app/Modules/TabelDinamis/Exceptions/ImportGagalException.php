<?php

namespace App\Modules\TabelDinamis\Exceptions;

use Exception;

/**
 * Exception yang dilempar ketika validasi atau proses impor berkas Excel gagal.
 * Menyimpan daftar galat spesifik per nomor baris Excel.
 */
class ImportGagalException extends Exception
{
    /**
     * @param string $pesan Pesan utama kegagalan
     * @param array<int, string> $daftarGalat Daftar rincian galat per baris Excel
     */
    public function __construct(
        string $pesan = 'Proses impor berkas gagal.',
        protected array $daftarGalat = [],
        int $code = 422,
        ?Exception $previous = null
    ) {
        parent::__construct($pesan, $code, $previous);
    }

    /**
     * Dapatkan daftar rincian galat per baris Excel.
     *
     * @return array<int, string>
     */
    public function getDaftarGalat(): array
    {
        return $this->daftarGalat;
    }
}


<?php

namespace App\Modules\TabelDinamis\Services;

use App\Modules\TabelDinamis\Exceptions\ImportGagalException;
use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Service untuk impor data indikator dari berkas Excel (.xlsx).
 *
 * Menerapkan Pseudocode 2A:
 * 1. Atomik (DB::transaction) — jika ada 1 baris salah, seluruh transaksi dibatalkan.
 * 2. Idempoten (upsert) — jika data diimpor berulang kali, tidak terjadi data dobel.
 * 3. Seluruh baris divalidasi SEBELUM menulis ke database.
 * 4. Menyebut nomor baris Excel asli pada setiap laporan galat.
 * 5. Menggunakan tabel berawalan td_ (td_indikator, td_wilayah, td_data_indikator).
 */
class ImportExcelService
{
    /**
     * Memproses berkas Excel dan melakukan validasi serta penyimpanan ke tabel td_data_indikator.
     *
     * @param string $filePath Path berkas spreadsheet
     * @return array ['sukses' => bool, 'pesan' => string, 'total' => int, 'errors' => array]
     * @throws ImportGagalException Jika validasi gagal
     */
    public function import(string $filePath): array
    {
        $maksGalat = config('tabel_dinamis.maks_error_impor', 100);
        $tahunMin = config('tabel_dinamis.tahun_min', 2021);
        $tahunMax = config('tabel_dinamis.tahun_max', 2026);

        // 1. Baca berkas spreadsheet
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } catch (Throwable $e) {
            throw new ImportGagalException(
                'Berkas tidak dapat dibaca atau rusak.',
                ['Format berkas tidak valid untuk dibaca oleh parser Excel: ' . $e->getMessage()]
            );
        }

        if (empty($rows) || count($rows) < 2) {
            throw new ImportGagalException(
                'Berkas kosong atau tidak memuat baris data.',
                ['Berkas harus memuat minimal baris 1 sebagai header dan baris 2 sebagai data.']
            );
        }

        // 2. Petakan kolom header (Baris 1)
        $header = array_map(function ($col) {
            return strtolower(trim((string)$col));
        }, $rows[0]);

        $colMap = $this->identifikasiKolom($header);
        $kolomWajib = ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis'];
        $kolomHilang = [];
        foreach ($kolomWajib as $f) {
            if (!isset($colMap[$f])) {
                $kolomHilang[] = $f;
            }
        }

        if (!empty($kolomHilang)) {
            throw new ImportGagalException(
                'Header kolom tidak lengkap.',
                ['Kolom wajib yang tidak ditemukan pada baris 1: ' . implode(', ', $kolomHilang)]
            );
        }

        // 3. Muat data referensi dari database (tabel td_indikator dan td_wilayah)
        $indikatorMap = Indikator::pluck('id', 'kode')->toArray(); // ['KODE' => id]
        $wilayahMap = Wilayah::all()->mapWithKeys(function ($w) {
            return [mb_strtolower(trim($w->nama)) => $w->id];
        })->toArray(); // ['nama_lower' => id]

        $daftarGalat = [];
        $dataValid = [];

        // 4. Tahap Validasi Baris demi Baris SEBELUM Simpan (Pseudocode 2A)
        for ($i = 1; $i < count($rows); $i++) {
            $nomorBaris = $i + 1; // 1-based index pada Excel asli
            $row = $rows[$i];

            // Abaikan jika baris benar-benar kosong
            if (empty(array_filter($row, fn($v) => $v !== null && trim((string)$v) !== ''))) {
                continue;
            }

            $kodeIndikator = trim((string)($row[$colMap['kode_indikator']] ?? ''));
            $namaWilayah = trim((string)($row[$colMap['nama_wilayah']] ?? ''));
            $tahunRaw = trim((string)($row[$colMap['tahun']] ?? ''));
            $jenis = trim((string)($row[$colMap['jenis']] ?? ''));
            $nilaiRaw = isset($colMap['nilai']) ? $row[$colMap['nilai']] : null;
            $sumber = isset($colMap['sumber']) ? trim((string)$row[$colMap['sumber']]) : null;

            // Validasi Kode Indikator (harus ada di tabel td_indikator)
            if ($kodeIndikator === '') {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Kode indikator wajib diisi.", $maksGalat);
            } elseif (!isset($indikatorMap[$kodeIndikator])) {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Kode indikator '{$kodeIndikator}' tidak ditemukan di database (tabel td_indikator).", $maksGalat);
            }

            // Validasi Wilayah (harus ada di tabel td_wilayah)
            $wilayahLower = mb_strtolower($namaWilayah);
            if ($namaWilayah === '') {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Nama wilayah wajib diisi.", $maksGalat);
            } elseif (!isset($wilayahMap[$wilayahLower])) {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Nama wilayah '{$namaWilayah}' tidak dikenali di daftar wilayah (tabel td_wilayah).", $maksGalat);
            }

            // Validasi Tahun (harus integer 2021-2026)
            $tahun = filter_var($tahunRaw, FILTER_VALIDATE_INT);
            if ($tahun === false || $tahun < $tahunMin || $tahun > $tahunMax) {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Tahun '{$tahunRaw}' tidak valid. Harus berupa angka tahun antara {$tahunMin} dan {$tahunMax}.", $maksGalat);
            }

            // Validasi Jenis (harus 'Realisasi' atau 'Target')
            $jenisNormalized = ucfirst(strtolower($jenis));
            if (!in_array($jenisNormalized, ['Realisasi', 'Target'])) {
                $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Jenis data '{$jenis}' tidak valid. Hanya diizinkan 'Realisasi' atau 'Target'.", $maksGalat);
            }

            // Validasi Nilai (nullable; jika ada harus numerik)
            $nilai = null;
            if ($nilaiRaw !== null && trim((string)$nilaiRaw) !== '' && trim((string)$nilaiRaw) !== '-') {
                $cleanNilai = str_replace(',', '.', trim((string)$nilaiRaw));
                if (!is_numeric($cleanNilai)) {
                    $this->catatGalat($daftarGalat, "Baris {$nomorBaris}: Nilai '{$nilaiRaw}' bukan angka numerik yang valid.", $maksGalat);
                } else {
                    $nilai = (float)$cleanNilai;
                }
            }

            // Hentikan jika mencapai batas pelaporan kesalahan
            if (count($daftarGalat) >= $maksGalat) {
                break;
            }

            if (empty($daftarGalat)) {
                $dataValid[] = [
                    'indikator_id' => $indikatorMap[$kodeIndikator] ?? null,
                    'wilayah_id' => $wilayahMap[$wilayahLower] ?? null,
                    'tahun' => $tahun,
                    'jenis' => $jenisNormalized,
                    'nilai' => $nilai,
                    'sumber' => $sumber ?: null,
                ];
            }
        }

        // 5. Lempar Exception jika ditemukan galat (Atomik: tidak ada baris yang tersimpan)
        if (!empty($daftarGalat)) {
            throw new ImportGagalException(
                'Ditemukan ' . count($daftarGalat) . ' kesalahan validasi pada berkas Excel. Seluruh proses impor dibatalkan.',
                $daftarGalat
            );
        }

        if (empty($dataValid)) {
            throw new ImportGagalException(
                'Tidak ada data valid yang ditemukan untuk diimpor.',
                ['Tidak ada baris data valid yang terbaca.']
            );
        }

        // 6. Simpan secara Atomik (DB::transaction) dan Idempoten (updateOrCreate pada tabel td_data_indikator)
        $totalDisimpan = 0;
        try {
            DB::transaction(function () use ($dataValid, &$totalDisimpan) {
                foreach ($dataValid as $row) {
                    DataIndikator::updateOrCreate(
                        [
                            'indikator_id' => $row['indikator_id'],
                            'wilayah_id' => $row['wilayah_id'],
                            'tahun' => $row['tahun'],
                            'jenis' => $row['jenis'],
                        ],
                        [
                            'nilai' => $row['nilai'],
                            'sumber' => $row['sumber'],
                        ]
                    );
                    $totalDisimpan++;
                }
            });
        } catch (Throwable $e) {
            throw new ImportGagalException(
                'Terjadi kesalahan basis data saat menyimpan data: ' . $e->getMessage(),
                [$e->getMessage()]
            );
        }

        return [
            'sukses' => true,
            'pesan' => "Berhasil mengimpor {$totalDisimpan} data indikator secara idempoten.",
            'total' => $totalDisimpan,
            'errors' => [],
        ];
    }

    /**
     * Mengunduh berkas template Excel (.xlsx) untuk panduan pengisian data impor.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Impor');

        // Header kolom
        $header = ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis', 'nilai', 'sumber'];
        $sheet->fromArray([$header], null, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Ambil contoh indikator dan wilayah pertama jika ada
        $contohIndikator = Indikator::first();
        $contohWilayah = Wilayah::first();

        $contohKode = $contohIndikator ? $contohIndikator->kode : 'CONTOH-KOMINFO-01';
        $contohNamaWilayah = $contohWilayah ? $contohWilayah->nama : 'Kota Ternate';

        // Baris contoh data
        $contohData = [
            [$contohKode, $contohNamaWilayah, 2023, 'Realisasi', 85.50, 'BPS Provinsi Maluku Utara'],
            [$contohKode, $contohNamaWilayah, 2024, 'Target', 88.00, 'Rencana Kerja BAPPEDA'],
        ];
        $sheet->fromArray($contohData, null, 'A2');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $namaBerkas = 'template_impor_indikator_ipw.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$namaBerkas}\"",
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Memetakan nama kolom di berkas Excel.
     */
    protected function identifikasiKolom(array $header): array
    {
        $map = [];

        foreach ($header as $idx => $name) {
            if (in_array($name, ['kode_indikator', 'kode', 'indikator_kode', 'indikator'])) {
                $map['kode_indikator'] = $idx;
            } elseif (in_array($name, ['nama_wilayah', 'wilayah', 'kabupaten', 'kota', 'kabupaten_kota'])) {
                $map['nama_wilayah'] = $idx;
            } elseif (in_array($name, ['tahun', 'thn', 'year'])) {
                $map['tahun'] = $idx;
            } elseif (in_array($name, ['jenis', 'tipe', 'jenis_data'])) {
                $map['jenis'] = $idx;
            } elseif (in_array($name, ['nilai', 'angka', 'value'])) {
                $map['nilai'] = $idx;
            } elseif (in_array($name, ['sumber', 'keterangan_sumber', 'source'])) {
                $map['sumber'] = $idx;
            }
        }

        return $map;
    }

    /**
     * Menambahkan pesan galat ke daftar jika belum mencapai batas maksimum.
     */
    protected function catatGalat(array &$daftar, string $pesan, int $maks): void
    {
        if (count($daftar) < $maks) {
            $daftar[] = $pesan;
        }
    }
}

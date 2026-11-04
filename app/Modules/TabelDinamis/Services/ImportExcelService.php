<?php

namespace App\Modules\TabelDinamis\Services;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Service untuk mengimpor data indikator dari file Excel / CSV.
 *
 * Aturan Bisnis & Keterbatasan:
 * 1. Atomik (DB::transaction) — jika ada baris error, tidak ada data yang disimpan.
 * 2. Idempoten (upsert) — data yang sudah ada akan diperbarui, yang baru akan ditambah.
 * 3. Seluruh validasi dilakukan SEBELUM menulis ke database.
 * 4. Laporan kesalahan mencantumkan nomor baris Excel asli (maksimal 100 kesalahan).
 * 5. KETERBATASAN: Impor hanya menambah dan memperbarui data, TIDAK MENGHAPUS data yang ada.
 */
class ImportExcelService
{
    /**
     * Memproses file spreadsheet dan melakukan validasi serta penyimpanan data.
     *
     * @param string $filePath Path absolut atau temporary file
     * @return array ['sukses' => bool, 'pesan' => string, 'total' => int, 'errors' => array]
     */
    public function import(string $filePath): array
    {
        $maksErrors = config('tabel_dinamis.maks_error_impor', 100);
        $tahunMin = config('tabel_dinamis.tahun_min', 2021);
        $tahunMax = config('tabel_dinamis.tahun_max', 2026);

        // 1. Baca spreadsheet
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } catch (Throwable $e) {
            return [
                'sukses' => false,
                'pesan' => 'Gagal membaca file: format file tidak valid atau rusak.',
                'total' => 0,
                'errors' => ['File tidak dapat dibaca oleh spreadsheet reader: ' . $e->getMessage()],
            ];
        }

        if (empty($rows) || count($rows) < 2) {
            return [
                'sukses' => false,
                'pesan' => 'File kosong atau tidak memiliki baris data (minimal 1 baris header dan 1 baris data).',
                'total' => 0,
                'errors' => ['File kosong atau hanya berisi baris header.'],
            ];
        }

        // 2. Petakan header kolom
        $header = array_map(function ($col) {
            return strtolower(trim((string)$col));
        }, $rows[0]);

        $colMap = $this->identifikasiKolom($header);
        $wajib = ['kode_indikator', 'nama_wilayah', 'tahun', 'jenis'];
        $missing = [];
        foreach ($wajib as $f) {
            if (!isset($colMap[$f])) {
                $missing[] = $f;
            }
        }

        if (!empty($missing)) {
            return [
                'sukses' => false,
                'pesan' => 'Header kolom tidak lengkap. Kolom wajib: ' . implode(', ', $missing),
                'total' => 0,
                'errors' => ['Header kolom wajib yang tidak ditemukan: ' . implode(', ', $missing)],
            ];
        }

        // 3. Muat referensi indikator & wilayah ke memory untuk validasi cepat
        $indikatorMap = Indikator::pluck('id', 'kode')->toArray(); // ['KODE' => id]
        $wilayahMap = Wilayah::all()->mapWithKeys(function ($w) {
            return [mb_strtolower(trim($w->nama)) => $w->id];
        })->toArray(); // ['nama_lower' => id]

        $errors = [];
        $dataValid = [];

        // 4. Tahap Validasi Baris demi Baris SEBELUM Simpan
        for ($i = 1; $i < count($rows); $i++) {
            $rowNum = $i + 1; // 1-based index di Excel
            $row = $rows[$i];

            // Abaikan baris kosong total
            if (empty(array_filter($row, fn($val) => $val !== null && trim((string)$val) !== ''))) {
                continue;
            }

            $kodeIndikator = trim((string)($row[$colMap['kode_indikator']] ?? ''));
            $namaWilayah = trim((string)($row[$colMap['nama_wilayah']] ?? ''));
            $tahunRaw = trim((string)($row[$colMap['tahun']] ?? ''));
            $jenis = trim((string)($row[$colMap['jenis']] ?? ''));
            $nilaiRaw = isset($colMap['nilai']) ? $row[$colMap['nilai']] : null;
            $sumber = isset($colMap['sumber']) ? trim((string)$row[$colMap['sumber']]) : null;

            // Validasi Kode Indikator
            if ($kodeIndikator === '') {
                $this->tambahError($errors, "Baris {$rowNum}: Kode indikator wajib diisi.", $maksErrors);
            } elseif (!isset($indikatorMap[$kodeIndikator])) {
                $this->tambahError($errors, "Baris {$rowNum}: Kode indikator '{$kodeIndikator}' tidak ditemukan di database.", $maksErrors);
            }

            // Validasi Wilayah
            $wilayahLower = mb_strtolower($namaWilayah);
            if ($namaWilayah === '') {
                $this->tambahError($errors, "Baris {$rowNum}: Nama wilayah wajib diisi.", $maksErrors);
            } elseif (!isset($wilayahMap[$wilayahLower])) {
                $this->tambahError($errors, "Baris {$rowNum}: Wilayah '{$namaWilayah}' tidak dikenali dalam daftar wilayah resmi.", $maksErrors);
            }

            // Validasi Tahun
            $tahun = filter_var($tahunRaw, FILTER_VALIDATE_INT);
            if ($tahun === false || $tahun < $tahunMin || $tahun > $tahunMax) {
                $this->tambahError($errors, "Baris {$rowNum}: Tahun '{$tahunRaw}' tidak valid. Harus angka antara {$tahunMin} dan {$tahunMax}.", $maksErrors);
            }

            // Validasi Jenis
            $jenisNormalized = ucfirst(strtolower($jenis));
            if (!in_array($jenisNormalized, ['Realisasi', 'Target'])) {
                $this->tambahError($errors, "Baris {$rowNum}: Jenis data '{$jenis}' tidak valid. Hanya diizinkan 'Realisasi' atau 'Target'.", $maksErrors);
            }

            // Validasi Nilai (boleh null / kosong, tapi jika diisi harus numerik)
            $nilai = null;
            if ($nilaiRaw !== null && trim((string)$nilaiRaw) !== '' && trim((string)$nilaiRaw) !== '-') {
                // Ubah koma ke titik jika format lokal Indonesia
                $cleanNilai = str_replace(',', '.', trim((string)$nilaiRaw));
                if (!is_numeric($cleanNilai)) {
                    $this->tambahError($errors, "Baris {$rowNum}: Nilai '{$nilaiRaw}' bukan angka numerik yang valid.", $maksErrors);
                } else {
                    $nilai = (float)$cleanNilai;
                }
            }

            // Jika limit error tercapai, hentikan looping
            if (count($errors) >= $maksErrors) {
                break;
            }

            // Simpan baris yang lolos parsing sementara
            if (empty($errors)) {
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

        // 5. Jika ada error validasi sama sekali, BATALKAN SEMUA (Atomik)
        if (!empty($errors)) {
            return [
                'sukses' => false,
                'pesan' => 'Ditemukan ' . count($errors) . ' kesalahan validasi pada file Excel. Tidak ada data yang disimpan.',
                'total' => 0,
                'errors' => $errors,
            ];
        }

        if (empty($dataValid)) {
            return [
                'sukses' => false,
                'pesan' => 'Tidak ada data valid yang ditemukan untuk diimpor.',
                'total' => 0,
                'errors' => ['Tidak ada baris data yang valid.'],
            ];
        }

        // 6. Tulis ke Database secara Atomik (Transaction + Upsert Idempoten)
        $totalTersimpan = 0;
        try {
            DB::transaction(function () use ($dataValid, &$totalTersimpan) {
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
                    $totalTersimpan++;
                }
            });
        } catch (Throwable $e) {
            return [
                'sukses' => false,
                'pesan' => 'Terjadi kesalahan basis data saat menyimpan data: ' . $e->getMessage(),
                'total' => 0,
                'errors' => [$e->getMessage()],
            ];
        }

        return [
            'sukses' => true,
            'pesan' => "Berhasil mengimpor {$totalTersimpan} data indikator secara idempoten.",
            'total' => $totalTersimpan,
            'errors' => [],
        ];
    }

    /**
     * Memetakan nama kolom di file dengan kolom yang dibutuhkan.
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
     * Menambahkan pesan error dengan batas maksimum.
     */
    protected function tambahError(array &$errors, string $pesan, int $maks): void
    {
        if (count($errors) < $maks) {
            $errors[] = $pesan;
        }
    }
}


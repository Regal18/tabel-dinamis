<?php

namespace App\Modules\TabelDinamis\Services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service untuk Export data tabel dinamis ke format Excel (XLSX) dan CSV.
 * Menerapkan spesifikasi Fase W5:
 * - XLSX: header bergabung, angka numerik asli, freeze pane, catatan keterangan
 * - CSV: UTF-8 BOM, pemisah titik koma (;)
 */
class ExportService
{
    /**
     * Ekspor data tabel ke response stream biner.
     *
     * @param string $format 'xlsx' atau 'csv'
     * @param array $tabelData Hasil dari TabelService::bangun
     * @return StreamedResponse
     */
    public function export(string $format, array $tabelData): StreamedResponse
    {
        $format = strtolower($format);
        $spreadsheet = $this->bangunSpreadsheet($tabelData, $format === 'csv');

        $namaFile = 'tabel-dinamis-' . date('Ymd-His') . '.' . $format;

        if ($format === 'csv') {
            $contentType = 'text/csv; charset=UTF-8';
            $writer = new Csv($spreadsheet);
            // Sesuai instruksi: UTF-8 BOM dan pemisah titik koma (;)
            $writer->setUseBOM(true);
            $writer->setDelimiter(';');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
        } else {
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $writer = new Xlsx($spreadsheet);
        }

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => $contentType,
                'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    /**
     * Membangun objek Spreadsheet dari struktur data tabel.
     */
    public function bangunSpreadsheet(array $tabelData, bool $isCsv = false): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tabel Dinamis');

        $grup = $tabelData['grup'] ?? [];
        $baris = $tabelData['baris'] ?? [];

        // ─── Format CSV (Header flat, pemisah titik koma, UTF-8 BOM) ─────────
        if ($isCsv) {
            $headerCsv = ['Kabupaten / Kota'];
            foreach ($grup as $g) {
                $satuan = !empty($g['satuan']) ? " ({$g['satuan']})" : '';
                foreach ($g['tahun'] as $th) {
                    $headerCsv[] = "[{$g['jenis']}] {$g['judul']}{$satuan} - {$th}";
                }
            }
            $sheet->fromArray([$headerCsv], null, 'A1');

            $rowIdx = 2;
            foreach ($baris as $b) {
                $rowVal = [$b['wilayah']];
                foreach ($b['nilai'] as $grupNilai) {
                    foreach ($grupNilai as $val) {
                        $rowVal[] = $val !== null ? $val : '-';
                    }
                }
                $sheet->fromArray([$rowVal], null, "A{$rowIdx}");
                $rowIdx++;
            }

            return $spreadsheet;
        }

        // ─── Format XLSX (Header Bergabung, Freeze Pane, Angka Numerik, Catatan) ───

        // 1. Header Tingkat 1 & Tingkat 2 (Header Bergabung)
        $sheet->setCellValue('A1', 'Kabupaten / Kota');
        $sheet->mergeCells('A1:A2');

        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $colNumber = 2; // Mulai kolom B
        foreach ($grup as $g) {
            $startCol = $colNumber;
            $satuan = !empty($g['satuan']) ? " ({$g['satuan']})" : '';
            $judulGrup = "[{$g['jenis']}] {$g['judul']}{$satuan}";

            foreach ($g['tahun'] as $th) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colNumber);
                $sheet->setCellValue("{$colLetter}2", $th);
                $sheet->getStyle("{$colLetter}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colNumber++;
            }

            $endCol = $colNumber - 1;
            $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
            $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

            // Merge cells untuk grup indikator di atas kolom tahun-tahunnya
            if ($startCol < $endCol) {
                $sheet->mergeCells("{$startColLetter}1:{$endColLetter}1");
            }
            $sheet->setCellValue("{$startColLetter}1", $judulGrup);
            $sheet->getStyle("{$startColLetter}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Styling Header
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $colNumber - 1));
        $headerRange = "A1:{$lastColLetter}2";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        // 2. Freeze Pane di B3 (Membekukan kolom A dan baris 1-2)
        $sheet->freezePane('B3');

        // 3. Isi Data Baris (Angka Tetap Numerik)
        $rowIdx = 3;
        foreach ($baris as $b) {
            $sheet->setCellValue("A{$rowIdx}", $b['wilayah']);
            $currentCol = 2;

            foreach ($b['nilai'] as $grupNilai) {
                foreach ($grupNilai as $val) {
                    $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
                    if ($val === null) {
                        $sheet->setCellValue("{$cLetter}{$rowIdx}", '-');
                        $sheet->getStyle("{$cLetter}{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } else {
                        // Angka tetap bertipe numerik asli
                        $numericVal = (float)$val;
                        $sheet->setCellValueExplicit("{$cLetter}{$rowIdx}", $numericVal, DataType::TYPE_NUMERIC);
                        $sheet->getStyle("{$cLetter}{$rowIdx}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle("{$cLetter}{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    $currentCol++;
                }
            }
            $rowIdx++;
        }

        // Terapkan border ke tabel data
        $lastDataRow = max(2, $rowIdx - 1);
        $sheet->getStyle("A1:{$lastColLetter}{$lastDataRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 4. Catatan Keterangan di Bawah Tabel
        $catatanRow1 = $lastDataRow + 2;
        $catatanRow2 = $catatanRow1 + 1;

        $sheet->setCellValue("A{$catatanRow1}", "Catatan: Nilai '-' menunjukkan data belum tersedia atau belum dipublikasikan.");
        $sheet->getStyle("A{$catatanRow1}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("A{$catatanRow1}")->getFont()->getColor()->setARGB('FF64748B');

        $sheet->setCellValue("A{$catatanRow2}", "Sumber: Indikator Bidang IPW BAPPEDA Provinsi Maluku Utara.");
        $sheet->getStyle("A{$catatanRow2}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("A{$catatanRow2}")->getFont()->getColor()->setARGB('FF64748B');

        // Atur Lebar Kolom
        $sheet->getColumnDimension('A')->setAutoSize(true);
        for ($c = 2; $c < $colNumber; $c++) {
            $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($cLetter)->setWidth(14);
        }

        return $spreadsheet;
    }
}

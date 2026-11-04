<?php

namespace App\Modules\TabelDinamis\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service untuk Export data tabel dinamis ke format Excel (XLSX) dan CSV.
 * Logika murni dan stateless.
 */
class ExportService
{
    /**
     * Ekspor data tabel ke response stream biner.
     *
     * @param string $format 'xlsx' atau 'csv'
     * @param array $tabelData Hasil dari TabelService::bangunTabel
     * @return StreamedResponse
     */
    public function export(string $format, array $tabelData): StreamedResponse
    {
        $format = strtolower($format);
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        $spreadsheet = $this->bangunSpreadsheet($tabelData, $format === 'csv');

        $namaFile = 'tabel-dinamis-' . date('Ymd-His') . '.' . $format;

        if ($format === 'csv') {
            $contentType = 'text/csv; charset=UTF-8';
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setUseBOM(true);
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

        // Hitung total kolom tahun
        $totalKolomTahun = 0;
        foreach ($grup as $g) {
            $totalKolomTahun += count($g['tahun'] ?? []);
        }

        if ($isCsv) {
            // Untuk CSV, buat header flat 1 baris agar mudah dibaca parser CSV
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

        // --- Format XLSX (Multi-level header bergaya resmi) ---
        $sheet->setCellValue('A1', 'Kabupaten / Kota');
        $sheet->mergeCells('A1:A2');

        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $colNumber = 2; // Kolom B adalah 2
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

            if ($startCol < $endCol) {
                $sheet->mergeCells("{$startColLetter}1:{$endColLetter}1");
            }
            $sheet->setCellValue("{$startColLetter}1", $judulGrup);
            $sheet->getStyle("{$startColLetter}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Terapkan style header
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $colNumber - 1));
        $headerRange = "A1:{$lastColLetter}2";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE9ECEF');

        // Isi Data Baris
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
                        $sheet->setCellValue("{$cLetter}{$rowIdx}", (float)$val);
                        $sheet->getStyle("{$cLetter}{$rowIdx}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle("{$cLetter}{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    $currentCol++;
                }
            }
            $rowIdx++;
        }

        // Terapkan border ke seluruh tabel
        $lastRow = max(2, $rowIdx - 1);
        $sheet->getStyle("A1:{$lastColLetter}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size kolom A
        $sheet->getColumnDimension('A')->setAutoSize(true);
        for ($c = 2; $c < $colNumber; $c++) {
            $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($cLetter)->setWidth(12);
        }

        return $spreadsheet;
    }
}


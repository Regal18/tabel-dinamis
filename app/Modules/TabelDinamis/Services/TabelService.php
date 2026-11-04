<?php

namespace App\Modules\TabelDinamis\Services;

use App\Modules\TabelDinamis\Models\DataIndikator;
use App\Modules\TabelDinamis\Models\Indikator;
use App\Modules\TabelDinamis\Models\Wilayah;

/**
 * Service logika bisnis utama: membangun data tabel dinamis.
 *
 * Digunakan oleh controller API dan nantinya controller mobile.
 * TIDAK memakai session() — stateless-ready.
 */
class TabelService
{
    /**
     * Bangun data tabel berdasarkan permintaan pengguna.
     *
     * @param array|null $wilayahIds ID wilayah yang dipilih, null = semua
     * @param array $dataPermintaan Array [{indikator_id, jenis, tahun: [int]}] (maks 2)
     * @return array {grup: [...], baris: [...]}
     */
    public function bangunTabel(?array $wilayahIds, array $dataPermintaan): array
    {
        // Ambil daftar wilayah
        $wilayahQuery = Wilayah::orderBy('nama');
        if ($wilayahIds !== null && count($wilayahIds) > 0) {
            $wilayahQuery->whereIn('id', $wilayahIds);
        }
        $daftarWilayah = $wilayahQuery->get();

        // Bangun metadata grup
        $grup = [];
        foreach ($dataPermintaan as $item) {
            $indikator = Indikator::find($item['indikator_id']);
            if (! $indikator) {
                continue;
            }

            $tahunTerurut = collect($item['tahun'])->sort()->values()->all();

            $grup[] = [
                'judul' => $indikator->label,
                'satuan' => $indikator->satuan,
                'jenis' => $item['jenis'],
                'tahun' => $tahunTerurut,
            ];
        }

        // Ambil semua data yang relevan sekaligus (batch query, bukan N+1)
        $semuaData = $this->ambilSemuaData($dataPermintaan, $daftarWilayah->pluck('id')->all());

        // Bangun baris per wilayah
        $baris = [];
        foreach ($daftarWilayah as $wilayah) {
            $nilai = [];

            foreach ($dataPermintaan as $gIdx => $item) {
                $nilaiGrup = [];
                $tahunTerurut = collect($item['tahun'])->sort()->values();

                foreach ($tahunTerurut as $tahun) {
                    $kunci = $this->kunciData(
                        $item['indikator_id'],
                        $wilayah->id,
                        $tahun,
                        $item['jenis']
                    );

                    $nilaiGrup[] = $semuaData[$kunci] ?? null;
                }

                $nilai[] = $nilaiGrup;
            }

            $baris[] = [
                'wilayah' => $wilayah->nama,
                'nilai' => $nilai,
            ];
        }

        return [
            'grup' => $grup,
            'baris' => $baris,
        ];
    }

    /**
     * Ambil semua data relevan dalam satu query, hasilkan map kunci -> nilai.
     *
     * @return array<string, float|null>
     */
    protected function ambilSemuaData(array $dataPermintaan, array $wilayahIds): array
    {
        $query = DataIndikator::query();

        // Bangun OR condition per grup permintaan
        $query->where(function ($qb) use ($dataPermintaan) {
            foreach ($dataPermintaan as $item) {
                $qb->orWhere(function ($sub) use ($item) {
                    $sub->where('indikator_id', $item['indikator_id'])
                        ->where('jenis', $item['jenis'])
                        ->whereIn('tahun', $item['tahun']);
                });
            }
        });

        if (count($wilayahIds) > 0) {
            $query->whereIn('wilayah_id', $wilayahIds);
        }

        $hasil = [];
        foreach ($query->get() as $row) {
            $kunci = $this->kunciData(
                $row->indikator_id,
                $row->wilayah_id,
                $row->tahun,
                $row->jenis
            );
            // Konversi nilai ke float jika bukan null
            $hasil[$kunci] = $row->nilai !== null ? (float) $row->nilai : null;
        }

        return $hasil;
    }

    /**
     * Buat kunci unik untuk lookup data.
     */
    protected function kunciData(int $indikatorId, int $wilayahId, int $tahun, string $jenis): string
    {
        return "{$indikatorId}-{$wilayahId}-{$tahun}-{$jenis}";
    }
}


<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk mencatat log riwayat impor berkas Excel admin (Audit Trail).
 */
class RiwayatImpor extends Model
{
    protected $table = 'td_riwayat_impor';

    protected $fillable = [
        'nama_berkas',
        'ukuran_kb',
        'diunggah_oleh',
        'total_baris',
        'status',
        'pesan',
    ];

    protected $casts = [
        'ukuran_kb' => 'integer',
        'total_baris' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

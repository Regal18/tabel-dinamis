<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DataIndikator — nilai data indikator per wilayah, tahun, dan jenis.
 */
class DataIndikator extends Model
{
    protected $table = 'td_data_indikator';

    protected $fillable = [
        'indikator_id',
        'wilayah_id',
        'tahun',
        'jenis',
        'nilai',
        'sumber',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'nilai' => 'decimal:4',
        ];
    }

    /**
     * Relasi: Data milik satu Indikator.
     */
    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }

    /**
     * Relasi: Data milik satu Wilayah.
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }
}


<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Wilayah — daftar kabupaten/kota.
 */
class Wilayah extends Model
{
    protected $table = 'td_wilayah';

    protected $fillable = ['nama'];

    /**
     * Relasi: Wilayah memiliki banyak data indikator.
     */
    public function dataIndikator(): HasMany
    {
        return $this->hasMany(DataIndikator::class, 'wilayah_id');
    }
}


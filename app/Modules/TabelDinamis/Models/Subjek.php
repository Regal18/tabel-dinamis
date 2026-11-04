<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Subjek — kategori indikator.
 */
class Subjek extends Model
{
    protected $table = 'td_subjek';

    protected $fillable = ['nama'];

    /**
     * Relasi: Subjek memiliki banyak indikator.
     */
    public function indikator(): HasMany
    {
        return $this->hasMany(Indikator::class, 'subjek_id');
    }
}


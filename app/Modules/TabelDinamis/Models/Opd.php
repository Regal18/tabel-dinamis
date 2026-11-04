<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model OPD (Organisasi Perangkat Daerah).
 */
class Opd extends Model
{
    protected $table = 'td_opd';

    protected $fillable = ['nama'];

    /**
     * Relasi: OPD memiliki banyak indikator.
     */
    public function indikator(): HasMany
    {
        return $this->hasMany(Indikator::class, 'opd_id');
    }
}


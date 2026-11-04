<?php

namespace App\Modules\TabelDinamis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Indikator — indikator dan sub-indikator pembangunan.
 */
class Indikator extends Model
{
    protected $table = 'td_indikator';

    protected $fillable = [
        'kode',
        'opd_id',
        'subjek_id',
        'nama',
        'sub_indikator',
        'satuan',
        'keterangan',
    ];

    /**
     * Relasi: Indikator milik satu OPD.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    /**
     * Relasi: Indikator milik satu Subjek.
     */
    public function subjek(): BelongsTo
    {
        return $this->belongsTo(Subjek::class, 'subjek_id');
    }

    /**
     * Relasi: Indikator memiliki banyak data.
     */
    public function dataIndikator(): HasMany
    {
        return $this->hasMany(DataIndikator::class, 'indikator_id');
    }

    /**
     * Menghasilkan label tampilan: "nama — sub_indikator" jika ada sub, atau "nama".
     */
    public function getLabelAttribute(): string
    {
        if ($this->sub_indikator) {
            return "{$this->nama} — {$this->sub_indikator}";
        }

        return $this->nama;
    }
}


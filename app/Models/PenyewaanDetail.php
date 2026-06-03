<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyewaanDetail extends Model
{
    protected $table = 'penyewaan_detail';
    protected $primaryKey = 'penyewaan_detail_id';

    protected $fillable = [
        'penyewaan_detail_penyewaan_id',
        'penyewaan_detail_alat_id',
        'penyewaan_detail_jumlah',
        'penyewaan_detail_subharga',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Satu detail dimiliki satu penyewaan.
     */
    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }

    /**
     * Satu detail dimiliki satu alat.
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'penyewaan_detail_alat_id', 'alat_id');
    }
}

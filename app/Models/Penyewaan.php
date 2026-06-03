<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penyewaan extends Model
{
    protected $table = 'penyewaan';
    protected $primaryKey = 'penyewaan_id';

    protected $fillable = [
        'penyewaan_pelanggan_id',
        'penyewaan_tglsewa',
        'penyewaan_tglkembali',
        'penyewaan_sttspembayaran',
        'penyewaan_sttskembali',
        'penyewaan_totalharga',
    ];

    protected $casts = [
        'penyewaan_tglsewa'    => 'date',
        'penyewaan_tglkembali' => 'date',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Satu penyewaan dimiliki satu pelanggan.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }

    /**
     * Satu penyewaan memiliki banyak detail.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }
}

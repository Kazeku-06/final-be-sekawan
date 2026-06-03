<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';
    protected $primaryKey = 'alat_id';

    protected $fillable = [
        'alat_kategori_id',
        'alat_nama',
        'alat_deskripsi',
        'alat_hargaperhari',
        'alat_stok',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Satu alat dimiliki satu kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'alat_kategori_id', 'kategori_id');
    }

    /**
     * Satu alat dapat muncul di banyak penyewaan_detail.
     */
    public function penyewaanDetail(): HasMany
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_detail_alat_id', 'alat_id');
    }
}

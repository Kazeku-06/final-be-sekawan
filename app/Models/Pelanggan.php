<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'pelanggan_id';

    protected $fillable = [
        'pelanggan_nama',
        'pelanggan_alamat',
        'pelanggan_notelp',
        'pelanggan_email',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /**
     * Satu pelanggan memiliki banyak pelanggan_data (dokumen identitas).
     */
    public function data(): HasMany
    {
        return $this->hasMany(PelangganData::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

    /**
     * Satu pelanggan memiliki banyak penyewaan.
     */
    public function penyewaan(): HasMany
    {
        return $this->hasMany(Penyewaan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }
}

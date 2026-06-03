<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenyewaanService
{
    /**
     * Buat transaksi penyewaan baru beserta detail dan kurangi stok.
     *
     * @throws \Exception
     */
    public function createPenyewaan(array $data): Penyewaan
    {
        return DB::transaction(function () use ($data) {
            $tglSewa    = Carbon::parse($data['penyewaan_tglsewa']);
            $tglKembali = Carbon::parse($data['penyewaan_tglkembali']);
            $durasi     = $tglSewa->diffInDays($tglKembali);

            if ($durasi < 1) {
                throw new \Exception('Durasi sewa minimal 1 hari.');
            }

            $totalHarga = 0;
            $detailsData = [];

            // Validasi stok dan hitung subharga tiap alat
            foreach ($data['details'] as $item) {
                /** @var Alat $alat */
                $alat = Alat::lockForUpdate()->find($item['alat_id']);

                if (!$alat) {
                    throw new \Exception("Alat dengan ID {$item['alat_id']} tidak ditemukan.");
                }

                if ($alat->alat_stok < $item['jumlah']) {
                    throw new \Exception(
                        "Stok alat '{$alat->alat_nama}' tidak mencukupi. " .
                        "Tersedia: {$alat->alat_stok}, diminta: {$item['jumlah']}."
                    );
                }

                // subharga = harga_perhari × jumlah × durasi
                $subharga    = $alat->alat_hargaperhari * $item['jumlah'] * $durasi;
                $totalHarga += $subharga;

                $detailsData[] = [
                    'alat'     => $alat,
                    'jumlah'   => $item['jumlah'],
                    'subharga' => $subharga,
                ];
            }

            // Simpan header penyewaan
            $penyewaan = Penyewaan::create([
                'penyewaan_pelanggan_id'   => $data['penyewaan_pelanggan_id'],
                'penyewaan_tglsewa'        => $data['penyewaan_tglsewa'],
                'penyewaan_tglkembali'     => $data['penyewaan_tglkembali'],
                'penyewaan_sttspembayaran' => $data['penyewaan_sttspembayaran'] ?? 'Belum Dibayar',
                'penyewaan_sttskembali'    => 'Belum Kembali',
                'penyewaan_totalharga'     => $totalHarga,
            ]);

            // Simpan detail dan kurangi stok
            foreach ($detailsData as $item) {
                PenyewaanDetail::create([
                    'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
                    'penyewaan_detail_alat_id'      => $item['alat']->alat_id,
                    'penyewaan_detail_jumlah'       => $item['jumlah'],
                    'penyewaan_detail_subharga'     => $item['subharga'],
                ]);

                // Kurangi stok alat otomatis
                $item['alat']->decrement('alat_stok', $item['jumlah']);
            }

            return $penyewaan->load(['pelanggan', 'detail.alat.kategori']);
        });
    }
}

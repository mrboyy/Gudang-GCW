<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Stok;

class StokService
{
    public function tambahStok(Barang $barang, int $qty, ?string $nomorLot): void
    {
        $stok = Stok::where('id_barang', $barang->id)
            ->where('nomor_lot', $nomorLot)
            ->lockForUpdate()
            ->first();

        if ($stok) {
            $stok->increment('stok_akhir', $qty);
            $stok->update(['tanggal_update' => now()]);
        } else {
            Stok::create([
                'id_barang'      => $barang->id,
                'nomor_lot'      => $nomorLot,
                'stok_akhir'     => $qty,
                'tanggal_update' => now(),
            ]);
        }
    }

    public function kurangiStok(Barang $barang, int $qty, ?string $nomorLot): void
    {
        if ($nomorLot) {
            $stok = Stok::where('id_barang', $barang->id)
                ->where('nomor_lot', $nomorLot)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stok->stok_akhir < $qty) {
                throw new \Exception("Stok lot {$nomorLot} tidak cukup. Tersedia: {$stok->stok_akhir}");
            }

            $stok->decrement('stok_akhir', $qty);
        } else {
            $sisa = $qty;
            $stoks = Stok::where('id_barang', $barang->id)
                ->where('stok_akhir', '>', 0)
                ->orderBy('tanggal_update')
                ->lockForUpdate()
                ->get();

            foreach ($stoks as $stok) {
                if ($sisa <= 0) break;
                $ambil = min($stok->stok_akhir, $sisa);
                $stok->decrement('stok_akhir', $ambil);
                $sisa -= $ambil;
            }

            if ($sisa > 0) {
                throw new \Exception("Stok tidak cukup. Kekurangan: {$sisa}");
            }
        }
    }

    public function kembalikanStok(Barang $barang, int $qty, ?string $nomorLot, string $jenis): void
    {
        if (in_array($jenis, ['keluar'])) {
            $this->tambahStok($barang, $qty, $nomorLot);
        } else {
            $this->kurangiStok($barang, $qty, $nomorLot);
        }
    }
}

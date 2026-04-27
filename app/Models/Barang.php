<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = ['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function transaksis() { return $this->hasMany(Transaksi::class, 'id_barang'); }
    public function stoks() { return $this->hasMany(Stok::class, 'id_barang'); }

    public function getStok(): int
    {
        return $this->stoks()->sum('stok_akhir');
    }

    public function updateStok(string $jenis, int $qty, ?string $nomorLot): void
    {
        $stok = $this->stoks()->where('nomor_lot', $nomorLot)->first();

        if ($jenis === 'masuk') {
            if ($stok) {
                $stok->increment('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            } else {
                $this->stoks()->create([
                    'nomor_lot' => $nomorLot,
                    'stok_akhir' => $qty,
                    'tanggal_update' => now(),
                ]);
            }
        } else {
            if ($stok) {
                $stok->decrement('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            }
        }
    }

    public function isStokMinimum(): bool
    {
        return $this->getStok() <= $this->stok_minimum;
    }
}

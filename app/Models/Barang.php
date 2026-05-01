<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = ['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi', 'is_active', 'foto'];
    protected $casts = ['is_active' => 'boolean'];

    public function transaksis() { return $this->hasMany(Transaksi::class, 'id_barang'); }
    public function stoks() { return $this->hasMany(Stok::class, 'id_barang'); }

    public function getStok(): int
    {
        return $this->stoks()->sum('stok_akhir');
    }

    public function updateStok(string $jenis, int $qty, ?string $nomorLot): void
    {
        if ($jenis === 'masuk') {
            $stok = $this->stoks()->where('nomor_lot', $nomorLot)->lockForUpdate()->first();
            if ($stok) {
                $stok->increment('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            } else {
                $this->stoks()->create([
                    'nomor_lot'      => $nomorLot,
                    'stok_akhir'     => $qty,
                    'tanggal_update' => now(),
                ]);
            }
            return;
        }

        // keluar: lot spesifik
        if ($nomorLot) {
            $stok = $this->stoks()->where('nomor_lot', $nomorLot)->lockForUpdate()->first();
            if ($stok) {
                $stok->decrement('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            }
            return;
        }

        // keluar FIFO: named lots first (ascending), null lots last
        $remaining = $qty;
        $lots = $this->stoks()
            ->where('stok_akhir', '>', 0)
            ->orderByRaw('nomor_lot IS NULL ASC')
            ->orderBy('nomor_lot')
            ->lockForUpdate()
            ->get();

        foreach ($lots as $lot) {
            if ($remaining <= 0) break;
            $deduct = min($lot->stok_akhir, $remaining);
            $lot->decrement('stok_akhir', $deduct);
            $lot->update(['tanggal_update' => now()]);
            $remaining -= $deduct;
        }
    }

    public function reverseStok(string $jenis, int $qty, ?string $nomorLot): void
    {
        // masuk/retur menambah stok → reverse: kurangi
        // keluar mengurangi stok → reverse: tambah
        $wasIncrease = in_array($jenis, ['masuk', 'retur_customer', 'retur_produksi']);

        if ($wasIncrease) {
            $stok = $this->stoks()->where('nomor_lot', $nomorLot)->lockForUpdate()->first();
            if ($stok) {
                $stok->decrement('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            }
        } else {
            $stok = $this->stoks()->where('nomor_lot', $nomorLot)->lockForUpdate()->first();
            if ($stok) {
                $stok->increment('stok_akhir', $qty);
                $stok->update(['tanggal_update' => now()]);
            } else {
                $this->stoks()->create([
                    'nomor_lot'      => $nomorLot,
                    'stok_akhir'     => $qty,
                    'tanggal_update' => now(),
                ]);
            }
        }
    }

    public function isStokMinimum(): bool
    {
        return $this->getStok() <= $this->stok_minimum;
    }
}

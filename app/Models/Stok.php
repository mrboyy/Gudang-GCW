<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $fillable = ['id_barang', 'nomor_lot', 'stok_akhir', 'tanggal_update'];
    protected $casts = ['tanggal_update' => 'datetime'];

    public function barang() { return $this->belongsTo(Barang::class, 'id_barang'); }

    public function addStock(int $qty): void
    {
        $this->increment('stok_akhir', $qty);
        $this->update(['tanggal_update' => now()]);
    }

    public function reduceStock(int $qty): void
    {
        $this->decrement('stok_akhir', $qty);
        $this->update(['tanggal_update' => now()]);
    }

    public function checkMin(): bool
    {
        return $this->stok_akhir <= $this->barang->stok_minimum;
    }
}

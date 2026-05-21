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

    public function isStokMinimum(): bool
    {
        return $this->getStok() <= $this->stok_minimum;
    }
}

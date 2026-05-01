<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $fillable = ['id_barang', 'nomor_lot', 'stok_akhir', 'tanggal_update'];
    protected $casts = ['tanggal_update' => 'datetime'];

    public function barang() { return $this->belongsTo(Barang::class, 'id_barang'); }
}

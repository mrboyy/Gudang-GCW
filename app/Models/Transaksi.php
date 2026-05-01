<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'no_transaksi', 'id_barang', 'id_user',
        'jenis_transaksi', 'tanggal', 'quantity', 'nomor_lot', 'keterangan',
        'is_void', 'void_by', 'void_at', 'void_reason',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'is_void'  => 'boolean',
        'void_at'  => 'datetime',
    ];

    public function barang() { return $this->belongsTo(Barang::class, 'id_barang'); }
    public function user() { return $this->belongsTo(User::class, 'id_user'); }
    public function voidUser() { return $this->belongsTo(User::class, 'void_by'); }

}

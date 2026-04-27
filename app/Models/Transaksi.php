<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'no_transaksi', 'id_barang', 'id_user',
        'jenis_transaksi', 'tanggal', 'quantity', 'nomor_lot', 'keterangan'
    ];

    protected $casts = ['tanggal' => 'date'];

    public function barang() { return $this->belongsTo(Barang::class, 'id_barang'); }
    public function user() { return $this->belongsTo(User::class, 'id_user'); }

    public function create_no_transaksi(string $jenis): string
    {
        $prefix = $jenis === 'masuk' ? 'BM' : 'BK';
        $count = self::whereDate('created_at', today())->where('jenis_transaksi', $jenis)->count() + 1;
        return $prefix . '-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

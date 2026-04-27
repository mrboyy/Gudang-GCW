<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = ['id_user', 'tanggal_generate', 'jenis_laporan', 'file_path'];
    protected $casts = ['tanggal_generate' => 'date'];

    public function user() { return $this->belongsTo(User::class, 'id_user'); }
}

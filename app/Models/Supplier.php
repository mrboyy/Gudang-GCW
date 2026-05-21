<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['nama'];

    public static function simpanJikaBaru(string $nama): void
    {
        $nama = trim($nama);
        if ($nama === '') return;
        static::firstOrCreate(['nama' => $nama]);
    }
}

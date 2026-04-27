<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['username' => 'admin',    'password' => Hash::make('admin123'),    'role' => 'admin',         'is_active' => true]);
        User::create(['username' => 'kepala',   'password' => Hash::make('kepala123'),   'role' => 'kepala_gudang', 'is_active' => true]);
        User::create(['username' => 'operator', 'password' => Hash::make('operator123'), 'role' => 'operator',      'is_active' => true]);

        $barangs = [
            ['kode_barang' => 'BRG-001', 'nama_barang' => 'Kleenoxide Disinfektan', 'merk' => 'Kleenoxide', 'satuan' => 'Liter',  'stok_minimum' => 10],
            ['kode_barang' => 'BRG-002', 'nama_barang' => 'Hand Sanitizer WHO',     'merk' => 'GCW',        'satuan' => 'Botol',  'stok_minimum' => 20],
            ['kode_barang' => 'BRG-003', 'nama_barang' => 'Masker Medis',           'merk' => 'Mediklin',   'satuan' => 'Box',    'stok_minimum' => 5],
            ['kode_barang' => 'BRG-004', 'nama_barang' => 'Sarung Tangan Latex',    'merk' => 'SafeGlove',  'satuan' => 'Box',    'stok_minimum' => 10],
            ['kode_barang' => 'BRG-005', 'nama_barang' => 'Baskuma Pro',            'merk' => 'Baskuma',    'satuan' => 'Unit',   'stok_minimum' => 2],
        ];

        $stokSamples = [
            ['BRG-001', 'LOT-2024-001', 50],
            ['BRG-002', 'LOT-2024-002', 100],
            ['BRG-003', 'LOT-2024-003', 25],
            ['BRG-004', 'LOT-2024-004', 8],
            ['BRG-005', 'LOT-2024-005', 3],
        ];

        foreach ($barangs as $i => $data) {
            $barang = Barang::create(array_merge($data, ['is_active' => true]));
            Stok::create([
                'id_barang'      => $barang->id,
                'nomor_lot'      => $stokSamples[$i][1],
                'stok_akhir'     => $stokSamples[$i][2],
                'tanggal_update' => now(),
            ]);
        }
    }
}

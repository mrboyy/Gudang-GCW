<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_barang'  => fake()->unique()->bothify('BRG-###'),
            'nama_barang'  => fake()->words(3, true),
            'satuan'       => 'pcs',
            'stok_minimum' => 10,
            'deskripsi'    => fake()->optional()->sentence(),
            'is_active'    => true,
            'foto'         => null,
        ];
    }
}

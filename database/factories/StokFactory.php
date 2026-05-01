<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stok>
 */
class StokFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_barang'      => \App\Models\Barang::factory(),
            'nomor_lot'      => fake()->bothify('LOT-###'),
            'stok_akhir'     => fake()->numberBetween(10, 200),
            'tanggal_update' => now(),
        ];
    }
}

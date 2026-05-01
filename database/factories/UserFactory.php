<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username'  => fake()->unique()->userName(),
            'email'     => fake()->unique()->safeEmail(),
            'password'  => bcrypt('Password1'),
            'role'      => 'operator',
            'is_active' => true,
        ];
    }
}

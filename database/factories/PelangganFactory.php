<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelanggan_nama' => fake()->name(),
            'pelanggan_alamat' => fake()->address(),
            'pelanggan_notelp' => fake()->numerify('08##########'),
            'pelanggan_email' => fake()->unique()->safeEmail(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PelangganData>
 */
class PelangganDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelanggan_data_pelanggan_id' => Pelanggan::factory(),
            'pelanggan_data_jenis' => fake()->randomElement(['KTP', 'SIM']),
            'pelanggan_data_file' => 'pelanggan_data/' . fake()->uuid() . '.png',
        ];
    }
}

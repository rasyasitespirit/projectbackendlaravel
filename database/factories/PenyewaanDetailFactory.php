<?php

namespace Database\Factories;

use App\Models\Penyewaan;
use App\Models\Alat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PenyewaanDetail>
 */
class PenyewaanDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jumlah = fake()->numberBetween(1, 5);
        $hargaPerHari = fake()->numberBetween(50000, 500000);
        $lamaSewa = fake()->numberBetween(1, 14);

        return [
            'penyewaan_detail_penyewaan_id' => Penyewaan::factory(),
            'penyewaan_detail_alat_id' => Alat::factory(),
            'penyewaan_detail_jumlah' => $jumlah,
            'penyewaan_detail_subharga' => $jumlah * $hargaPerHari * $lamaSewa,
        ];
    }
}

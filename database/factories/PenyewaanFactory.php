<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penyewaan>
 */
class PenyewaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalSewa = fake()->dateTimeBetween('-30 days', 'now');
        $tanggalKembali = fake()->dateTimeBetween($tanggalSewa, '+14 days');

        return [
            'penyewaan_pelanggan_id' => Pelanggan::factory(),
            'penyewaan_tglsewa' => $tanggalSewa,
            'penyewaan_tglkembali' => $tanggalKembali,
            'penyewaan_sttspembayaran' => fake()->randomElement(['Lunas', 'Belum Dibayar', 'DP']),
            'penyewaan_sttskembali' => fake()->randomElement(['Sudah Kembali', 'Belum Kembali']),
            'penyewaan_totalharga' => fake()->numberBetween(100000, 5000000),
        ];
    }
}

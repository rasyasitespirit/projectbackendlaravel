<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategoriList = [
            'Alat Berat',
            'Alat Listrik',
            'Alat Pertukangan',
            'Alat Kebun',
            'Alat Konstruksi',
            'Alat Elektronik',
        ];

        return [
            'kategori_nama' => fake()->unique()->randomElement($kategoriList),
        ];
    }
}

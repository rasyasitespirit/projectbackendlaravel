<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alat>
 */
class AlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $alatList = [
            'Bor Listrik',
            'Gerinda Tangan',
            'Mesin Las',
            'Gergaji Mesin',
            'Kompresor Udara',
            'Mesin Potong Rumput',
            'Excavator Mini',
            'Concrete Mixer',
            'Scaffolding',
            'Generator Listrik',
            'Mesin Cuci Tekanan Tinggi',
            'Tangga Lipat',
        ];

        return [
            'alat_kategori_id' => Kategori::factory(),
            'alat_nama' => fake()->randomElement($alatList),
            'alat_deskripsi' => fake()->sentence(15),
            'alat_hargaperhari' => fake()->numberBetween(50000, 500000),
            'alat_stok' => fake()->numberBetween(1, 20),
        ];
    }
}

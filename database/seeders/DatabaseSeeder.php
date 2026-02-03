<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\PelangganData;
use App\Models\Kategori;
use App\Models\Alat;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin
        $this->call([
            AdminSeeder::class,
        ]);

        // Seed Pelanggan (10 pelanggan)
        $pelanggans = Pelanggan::factory(10)->create();

        // Seed Pelanggan Data (untuk setiap pelanggan)
        foreach ($pelanggans as $pelanggan) {
            PelangganData::factory()->create([
                'pelanggan_data_pelanggan_id' => $pelanggan->pelanggan_id,
            ]);
        }

        // Seed Kategori (6 kategori)
        $kategoris = Kategori::factory(6)->create();

        // Seed Alat (20 alat dengan kategori yang sudah ada)
        $alats = [];
        foreach ($kategoris as $kategori) {
            for ($i = 0; $i < 3; $i++) {
                $alats[] = Alat::factory()->create([
                    'alat_kategori_id' => $kategori->kategori_id,
                ]);
            }
        }

        // Seed Penyewaan (15 penyewaan dengan pelanggan yang sudah ada)
        foreach ($pelanggans->random(8) as $pelanggan) {
            $penyewaan = Penyewaan::factory()->create([
                'penyewaan_pelanggan_id' => $pelanggan->pelanggan_id,
            ]);

            // Seed Penyewaan Detail (2-4 detail per penyewaan dengan alat yang sudah ada)
            $jumlahDetail = rand(2, 4);
            $totalHarga = 0;

            for ($i = 0; $i < $jumlahDetail; $i++) {
                $alat = collect($alats)->random();
                $jumlah = rand(1, 3);
                $lamaSewa = $penyewaan->penyewaan_tglkembali->diffInDays($penyewaan->penyewaan_tglsewa);
                $subtotal = $jumlah * $alat->alat_hargaperhari * $lamaSewa;
                $totalHarga += $subtotal;

                PenyewaanDetail::factory()->create([
                    'penyewaan_detail_penyewaan_id' => $penyewaan->penyewaan_id,
                    'penyewaan_detail_alat_id' => $alat->alat_id,
                    'penyewaan_detail_jumlah' => $jumlah,
                    'penyewaan_detail_subharga' => $subtotal,
                ]);
            }

            // Update total harga penyewaan
            $penyewaan->update([
                'penyewaan_totalharga' => $totalHarga,
            ]);
        }
    }
}

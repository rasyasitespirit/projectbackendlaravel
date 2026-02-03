<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        if (Admin::count() > 0) {
            $this->command->info('Admin sudah ada, skip seeding.');
            return;
        }

        // Buat admin default
        Admin::create([
            'admin_username' => 'superadmin',
            'admin_password' => Hash::make('password123'),
        ]);

        $this->command->info('✅ Admin berhasil dibuat!');
        $this->command->info('Username: superadmin');
        $this->command->info('Password: password123');
        $this->command->warn('⚠️  Jangan lupa ganti password di production!');
    }
}

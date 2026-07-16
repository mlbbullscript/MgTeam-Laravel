<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — Seeder utama yang memanggil semua seeder lain.
 *
 * Urutan penting: UserSeeder harus berjalan sebelum seeder lain
 * yang membutuhkan user (foreign key).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding database Bisnis Manajemen Tim...');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,    // 1. User dulu (diperlukan FK di seeder lain)
            SettingSeeder::class, // 2. Konfigurasi default
        ]);

        $this->command->newLine();
        $this->command->info('✅ Seeding selesai! Sistem siap digunakan.');
    }
}

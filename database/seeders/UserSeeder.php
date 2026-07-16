<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk user SuperAdmin default.
 *
 * Membuat satu akun SuperAdmin sebagai entry point awal sistem.
 * Password: password123 — HARUS DIGANTI segera setelah login pertama.
 *
 * Credentials default:
 *   Username : superadmin
 *   Password : password123
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah SuperAdmin sudah ada (hindari duplikasi saat re-seed)
        if (User::where('username', 'superadmin')->exists()) {
            $this->command->info('SuperAdmin sudah ada, skip membuat ulang.');
            return;
        }

        User::create([
            'username'   => 'superadmin',
            'password'   => Hash::make('password123'),
            'role'       => 'superadmin',
            'coin_saham' => 0,  // SuperAdmin tidak perlu coin saham
            'is_active'  => true,
        ]);

        $this->command->info('✓ User SuperAdmin berhasil dibuat. Username: superadmin | Password: password123');
        $this->command->warn('  ⚠️  SEGERA GANTI PASSWORD setelah login pertama!');
    }
}

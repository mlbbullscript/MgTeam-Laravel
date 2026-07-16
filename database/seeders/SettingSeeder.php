<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk konfigurasi default sistem.
 *
 * Konfigurasi yang wajib ada:
 *   - pct_saham         : persentase laba bersih untuk pool saham (default: 50%)
 *   - pct_kerja         : persentase laba bersih untuk pool kerja (default: 50%)
 *   - izin_upload_rekan : apakah rekan boleh upload laporan keuangan (default: false)
 *
 * Aturan bisnis: pct_saham + pct_kerja HARUS selalu = 100%.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturanDefault = [
            'pct_saham'          => '50',     // 50% laba bersih untuk pool saham
            'pct_kerja'          => '50',     // 50% laba bersih untuk pool kerja
            'izin_upload_rekan'  => 'false',  // Rekan belum bisa upload laporan secara default
        ];

        foreach ($pengaturanDefault as $kunci => $nilai) {
            Setting::updateOrCreate(
                ['key_name' => $kunci],
                ['value'    => $nilai]
            );
        }

        $this->command->info('✓ Setting default berhasil dibuat:');
        $this->command->info('  - pct_saham: 50%');
        $this->command->info('  - pct_kerja: 50%');
        $this->command->info('  - izin_upload_rekan: false');
    }
}

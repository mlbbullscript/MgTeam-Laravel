<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Setting
 *
 * Menyimpan konfigurasi global aplikasi sebagai key-value pairs.
 * Kunci yang wajib ada (di-seed):
 *   - pct_saham      : persentase pool saham dari laba bersih (default: 50)
 *   - pct_kerja      : persentase pool kerja dari laba bersih (default: 50)
 *   - izin_upload_rekan : apakah rekan boleh upload laporan ('true'/'false')
 *
 * @property int $id
 * @property string $key_name
 * @property string $value
 */
class Setting extends Model
{
    /**
     * Tidak menggunakan created_at — hanya updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'key_name',
        'value',
    ];

    // ===================== STATIC HELPER METHODS =====================

    /**
     * Ambil nilai setting berdasarkan key.
     * Mengembalikan null jika key tidak ditemukan.
     */
    public static function getValue(string $key): ?string
    {
        return static::where('key_name', $key)->value('value');
    }

    /**
     * Set nilai setting berdasarkan key.
     * Membuat record baru jika belum ada (upsert).
     */
    public static function setValue(string $key, string $value): void
    {
        static::updateOrCreate(
            ['key_name' => $key],
            ['value' => $value]
        );
    }

    /**
     * Cek apakah rekan diizinkan upload laporan keuangan.
     */
    public static function izinUploadRekan(): bool
    {
        return static::getValue('izin_upload_rekan') === 'true';
    }

    /**
     * Ambil persentase pool saham (dalam bentuk angka).
     */
    public static function pctSaham(): float
    {
        return (float) (static::getValue('pct_saham') ?? 50);
    }

    /**
     * Ambil persentase pool kerja (dalam bentuk angka).
     */
    public static function pctKerja(): float
    {
        return (float) (static::getValue('pct_kerja') ?? 50);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model User
 *
 * Mewakili semua pengguna sistem: SuperAdmin dan Rekan.
 * Tidak menggunakan email — autentikasi berdasarkan username + password.
 * coin_saham: porsi kepemilikan bisnis (total semua user = 100).
 * Poin kerja TIDAK disimpan di sini — dihitung dinamis (lihat ADR-002).
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $photo_profile
 * @property string $role  -- 'superadmin' | 'rekan'
 * @property float $coin_saham
 * @property bool $is_active
 */
class User extends Authenticatable
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass-assignment.
     */
    protected $fillable = [
        'username',
        'password',
        'photo_profile',
        'role',
        'coin_saham',
        'is_active',
        'points_resetted_at',
        'manual_points_adjustment',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi ke JSON.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Cast tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'password'                 => 'hashed',
            'coin_saham'               => 'decimal:2',
            'is_active'                => 'boolean',
            'points_resetted_at'       => 'datetime',
            'manual_points_adjustment' => 'decimal:2',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Laporan keuangan yang dibuat oleh user ini.
     */
    public function laporanKeuangan(): HasMany
    {
        return $this->hasMany(FinancialReport::class, 'created_by');
    }

    /**
     * Jadwal yang ditugaskan ke user ini.
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Schedule::class, 'assigned_to');
    }

    /**
     * Jadwal yang dibuat oleh user ini (sebagai SuperAdmin).
     */
    public function jadwalDibuat(): HasMany
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    /**
     * Tugas lembur yang diambil oleh user ini.
     */
    public function lemburDiambil(): HasMany
    {
        return $this->hasMany(OvertimeTask::class, 'taken_by');
    }

    /**
     * Tugas lembur yang dibuat oleh user ini (sebagai SuperAdmin).
     */
    public function lemburDibuat(): HasMany
    {
        return $this->hasMany(OvertimeTask::class, 'created_by');
    }

    /**
     * Rincian distribusi profit yang diterima user ini.
     */
    public function rincianProfit(): HasMany
    {
        return $this->hasMany(ProfitDistributionDetail::class, 'user_id');
    }

    /**
     * Distribusi profit yang dilakukan oleh user ini (sebagai SuperAdmin).
     */
    public function distribusiDilakukan(): HasMany
    {
        return $this->hasMany(ProfitDistribution::class, 'distributed_by');
    }

    // ===================== HELPER METHODS =====================

    /**
     * Cek apakah user adalah SuperAdmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Cek apakah user adalah Rekan.
     */
    public function isRekan(): bool
    {
        return $this->role === 'rekan';
    }

    /**
     * Dapatkan URL foto profil yang kompatibel dengan base64 maupun file storage statis.
     */
    public function getPhotoProfileUrlAttribute(): string
    {
        if (!$this->photo_profile) {
            return '';
        }
        if (str_starts_with($this->photo_profile, 'data:')) {
            return $this->photo_profile;
        }
        return asset('storage/' . $this->photo_profile);
    }
}

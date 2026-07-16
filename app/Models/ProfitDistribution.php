<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model ProfitDistribution
 *
 * Menyimpan riwayat distribusi profit yang dilakukan SuperAdmin.
 * Setiap distribusi memiliki snapshot persentase (pct_saham_used, pct_kerja_used)
 * agar riwayat tidak berubah jika pengaturan diubah di kemudian hari.
 *
 * @property int $id
 * @property int $distributed_by
 * @property float $laba_bersih
 * @property float $pct_saham_used
 * @property float $pct_kerja_used
 * @property float $total_pool_saham
 * @property float $total_pool_kerja
 * @property string|null $notes
 * @property \Carbon\Carbon $distributed_at
 */
class ProfitDistribution extends Model
{
    /**
     * Tidak menggunakan timestamps standar Laravel (created_at/updated_at).
     * Hanya menggunakan distributed_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'distributed_by',
        'laba_bersih',
        'pct_saham_used',
        'pct_kerja_used',
        'total_pool_saham',
        'total_pool_kerja',
        'notes',
        'distributed_at',
    ];

    protected function casts(): array
    {
        return [
            'laba_bersih'      => 'decimal:2',
            'pct_saham_used'   => 'decimal:2',
            'pct_kerja_used'   => 'decimal:2',
            'total_pool_saham' => 'decimal:2',
            'total_pool_kerja' => 'decimal:2',
            'distributed_at'   => 'datetime',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * SuperAdmin yang melakukan distribusi ini.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    /**
     * Detail per-user dari distribusi ini.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(ProfitDistributionDetail::class, 'distribution_id');
    }
}

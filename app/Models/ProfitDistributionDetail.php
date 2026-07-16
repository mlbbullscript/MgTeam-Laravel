<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ProfitDistributionDetail
 *
 * Detail distribusi profit per user.
 * Menyimpan snapshot coin_saham dan poin_kerja saat distribusi dilakukan.
 * Status: 'pending' = belum ditransfer, 'ditransfer' = sudah dibayarkan.
 *
 * @property int $id
 * @property int $distribution_id
 * @property int $user_id
 * @property float $coin_saham     -- snapshot saat distribusi
 * @property float $poin_kerja     -- snapshot saat distribusi
 * @property float $bagian_saham
 * @property float $bagian_kerja
 * @property float $total
 * @property string $status        -- 'pending' | 'ditransfer'
 * @property \Carbon\Carbon|null $transferred_at
 */
class ProfitDistributionDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'distribution_id',
        'user_id',
        'coin_saham',
        'poin_kerja',
        'bagian_saham',
        'bagian_kerja',
        'total',
        'status',
        'transferred_at',
    ];

    protected function casts(): array
    {
        return [
            'coin_saham'     => 'decimal:2',
            'poin_kerja'     => 'decimal:2',
            'bagian_saham'   => 'decimal:2',
            'bagian_kerja'   => 'decimal:2',
            'total'          => 'decimal:2',
            'transferred_at' => 'datetime',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Distribusi profit induk dari detail ini.
     */
    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(ProfitDistribution::class, 'distribution_id');
    }

    /**
     * User penerima profit ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ===================== HELPER METHODS =====================

    /**
     * Tandai detail ini sebagai sudah ditransfer.
     */
    public function tandaiDitransfer(): void
    {
        $this->update([
            'status'         => 'ditransfer',
            'transferred_at' => now(),
        ]);
    }
}

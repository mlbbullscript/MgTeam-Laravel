<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OvertimeTask (Tugas Lembur)
 *
 * Menyimpan tugas lembur yang bisa diambil rekan.
 * Mekanisme: First Come First Served dengan proteksi race condition.
 * Race condition protection: DB::transaction + lockForUpdate() di OvertimeTaskController.
 *
 * Status lifecycle: 'tersedia' → 'diambil'
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $poin_kerja
 * @property string $status  -- 'tersedia' | 'diambil'
 * @property int|null $taken_by
 * @property \Carbon\Carbon|null $taken_at
 * @property int $created_by
 */
class OvertimeTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'poin_kerja',
        'status',
        'taken_by',
        'taken_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'poin_kerja' => 'decimal:2',
            'taken_at'   => 'datetime',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * User yang mengambil tugas lembur ini.
     */
    public function pengambil(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    /**
     * User yang membuat tugas lembur ini (SuperAdmin).
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    /**
     * Filter hanya tugas lembur yang masih tersedia.
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    /**
     * Filter tugas lembur yang sudah diambil oleh user tertentu.
     */
    public function scopeDiambilOleh($query, int $userId)
    {
        return $query->where('status', 'diambil')->where('taken_by', $userId);
    }

    // ===================== HELPER METHODS =====================

    /**
     * Cek apakah tugas lembur masih tersedia untuk diambil.
     */
    public function masihTersedia(): bool
    {
        return $this->status === 'tersedia';
    }
}

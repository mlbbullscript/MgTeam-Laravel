<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model FinancialReport
 *
 * Menyimpan laporan arus keuangan (pemasukan & pengeluaran).
 * Menggunakan SoftDeletes — data tidak dihapus permanen, hanya ditandai deleted_at.
 * report_date adalah tanggal laporan aktual, bukan tanggal created_at.
 *
 * @property int $id
 * @property string $type  -- 'pemasukan' | 'pengeluaran'
 * @property string $name
 * @property string|null $description
 * @property float $amount
 * @property string|null $screenshot
 * @property int $created_by
 * @property \Carbon\Carbon $report_date
 */
class FinancialReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'description',
        'amount',
        'screenshot',
        'created_by',
        'report_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'report_date' => 'date',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * User yang membuat laporan ini.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    /**
     * Filter hanya pemasukan.
     */
    public function scopePemasukan($query)
    {
        return $query->where('type', 'pemasukan');
    }

    /**
     * Filter hanya pengeluaran.
     */
    public function scopePengeluaran($query)
    {
        return $query->where('type', 'pengeluaran');
    }

    /**
     * Filter berdasarkan rentang tanggal.
     */
    public function scopeTanggal($query, string $dari, string $sampai)
    {
        return $query->whereBetween('report_date', [$dari, $sampai]);
    }

    /**
     * Dapatkan URL screenshot yang kompatibel dengan base64 maupun file storage statis.
     */
    public function getScreenshotUrlAttribute(): string
    {
        if (!$this->screenshot) {
            return '';
        }
        if (str_starts_with($this->screenshot, 'data:')) {
            return $this->screenshot;
        }
        return asset('storage/' . $this->screenshot);
    }
}

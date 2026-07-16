<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Model Schedule
 *
 * Menyimpan jadwal kerja yang ditugaskan ke rekan/superadmin.
 * Jadwal yang sudah berlalu (schedule_date <= hari ini) berkontribusi ke poin kerja.
 * Poin kerja dihitung dinamis oleh PoinKerjaService (ADR-002).
 *
 * @property int $id
 * @property string $task_name
 * @property string|null $description
 * @property float $poin_kerja
 * @property int $assigned_to
 * @property \Carbon\Carbon $schedule_date
 * @property int $created_by
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'description',
        'poin_kerja',
        'assigned_to',
        'hari',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'poin_kerja'    => 'decimal:2',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * User yang ditugaskan untuk jadwal ini.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * User yang membuat jadwal ini (SuperAdmin).
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    /**
     * Filter jadwal untuk user tertentu.
     */
    public function scopeUntukUser($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}

<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use App\Models\OvertimeTask;
use Illuminate\Database\Eloquent\Collection;

/**
 * PoinKerjaService — Menghitung total poin kerja per user secara dinamis.
 *
 * Poin kerja = jadwal yang sudah berlalu (schedule_date <= hari ini)
 *            + tugas lembur yang sudah diambil user.
 * Tidak disimpan di DB — dihitung realtime (lihat ADR-002).
 */
class PoinKerjaService
{
    /**
     * Hitung total poin kerja untuk satu user.
     */
    public function hitungPoinUser(int $userId): float
    {
        $user = User::find($userId);
        if (!$user) return 0.0;

        $resetTime = $user->points_resetted_at;
        if ($resetTime) {
            $resetTime = $resetTime->tz('Asia/Jakarta');
        }

        // Ambil semua jadwal untuk user ini
        $schedules = Schedule::where('assigned_to', $userId)->get();
        $poinJadwal = 0.0;

        $today = now('Asia/Jakarta');

        foreach ($schedules as $schedule) {
            // Hitung tanggal mulai: max(points_resetted_at, schedule->created_at)
            $start = $schedule->created_at->tz('Asia/Jakarta');
            if ($resetTime && $resetTime->gt($start)) {
                $start = $resetTime;
            }

            $occurrences = $this->hitungKemunculanHari($start, $today, $schedule->hari);
            $poinJadwal += $occurrences * $schedule->poin_kerja;
        }

        // Poin Lembur
        $poinLemburQuery = OvertimeTask::where('taken_by', $userId)
            ->where('status', 'diambil');

        if ($resetTime) {
            $poinLemburQuery->where('taken_at', '>', $resetTime);
        }
        $poinLembur = $poinLemburQuery->sum('poin_kerja');

        return (float) ($poinJadwal + $poinLembur + (float) $user->manual_points_adjustment);
    }

    /**
     * Hitung berapa kali hari tertentu muncul antara $start dan $end (inklusif).
     */
    private function hitungKemunculanHari(\Carbon\Carbon $start, \Carbon\Carbon $end, string $hariName): int
    {
        $daysMapping = [
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
            'Minggu' => 0, // Sunday is 0 in Carbon's dayOfWeek
        ];

        if (!isset($daysMapping[$hariName])) {
            return 0;
        }

        $targetDayOfWeek = $daysMapping[$hariName];

        $start = $start->copy()->tz('Asia/Jakarta')->startOfDay();
        $end = $end->copy()->tz('Asia/Jakarta')->startOfDay();

        if ($start->gt($end)) {
            return 0;
        }

        // Cari kemunculan pertama dari targetDayOfWeek pada atau setelah $start
        $first = $start->copy();
        while ($first->dayOfWeek !== $targetDayOfWeek) {
            $first->addDay();
        }

        if ($first->gt($end)) {
            return 0;
        }

        // Jumlah hari dari $first ke $end
        $diffInDays = $first->diffInDays($end);

        return 1 + (int) floor($diffInDays / 7);
    }

    /**
     * Hitung poin kerja untuk semua user aktif sekaligus.
     * Mengembalikan array: [user_id => total_poin]
     */
    public function hitungSemuaUser(): array
    {
        $users = User::where('is_active', true)->get();
        $hasil = [];

        foreach ($users as $user) {
            $hasil[$user->id] = $this->hitungPoinUser($user->id);
        }

        return $hasil;
    }

    /**
     * Hitung total poin kerja semua user (digunakan sebagai penyebut kalkulasi profit).
     */
    public function totalSemuaPoin(): float
    {
        return (float) array_sum($this->hitungSemuaUser());
    }
}

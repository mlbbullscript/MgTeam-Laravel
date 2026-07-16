<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Services\PoinKerjaService;
use App\Services\ProfitService;
use App\Models\Schedule;
use App\Models\OvertimeTask;
use App\Models\FinancialReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * DashboardController Rekan
 * Menampilkan data personal rekan: poin kerja berjalan, porsi koin saham,
 * estimasi bagian profit, dan grafik akumulasi poin kerja 7 hari terakhir.
 * Ditambah transparansi data finansial global dari SuperAdmin.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly PoinKerjaService $poinKerjaService,
        private readonly ProfitService $profitService
    ) {}

    public function index(): View
    {
        $userId = auth()->id();
        $user = auth()->user();

        // =====================================================================
        // A. METRIK & GRAFIK PERSONAL REKAN
        // =====================================================================
        $poinSaya = $this->poinKerjaService->hitungPoinUser($userId);
        $coinSaham = $user->coin_saham;

        // Hitung estimasi bagian profit berjalan
        $breakdown = $this->profitService->hitungBreakdown();
        $bagianSaya = collect($breakdown['rekan'])->firstWhere(fn($r) => $r['user']->id === $userId);
        $estimasiProfit = $bagianSaya['total'] ?? 0.0;

        // Siapkan data chart garis (poin kerja harian 7 hari terakhir)
        $dates = [];
        $poinHari = [];

        $resetTime = $user->points_resetted_at;
        $userSchedules = Schedule::where('assigned_to', $userId)->get();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $d = Carbon::parse($date);
            $dayNameMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
            $hariName = $dayNameMap[$d->dayOfWeek];
            
            $dates[$date] = $d->translatedFormat('l'); // Nama hari: "Senin"
            $poinHari[$date] = 0.0;

            foreach ($userSchedules as $s) {
                if ($s->hari === $hariName) {
                    $start = $s->created_at->copy()->startOfDay();
                    if ($resetTime && $resetTime->gt($start)) {
                        $start = $resetTime->copy()->startOfDay();
                    }

                    if ($d->copy()->startOfDay()->gte($start)) {
                        $poinHari[$date] += (float) $s->poin_kerja;
                    }
                }
            }
        }

        // Ambil poin dari lembur yang diambil
        $overtimes = OvertimeTask::where('taken_by', $userId)
            ->where('status', 'diambil')
            ->whereBetween('taken_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(taken_at) as date, SUM(poin_kerja) as total')
            ->groupBy('date')
            ->get();

        foreach ($overtimes as $o) {
            if (isset($poinHari[$o->date])) {
                $poinHari[$o->date] += (float) $o->total;
            }
        }

        $chartLabels = array_values($dates);
        $chartData = array_values($poinHari);

        // =====================================================================
        // B. TRANSPARANSI DATA KEUANGAN GLOBAL & SAHAM (DARI SUPERADMIN)
        // =====================================================================
        $totalPemasukan  = (float) FinancialReport::pemasukan()->sum('amount');
        $totalPengeluaran = (float) FinancialReport::pengeluaran()->sum('amount');
        $labaBersih       = $totalPemasukan - $totalPengeluaran;

        // Siapkan data chart garis (arus keuangan 30 hari terakhir)
        $dates30 = [];
        $pemasukanData30 = [];
        $pengeluaranData30 = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dates30[$date] = Carbon::parse($date)->translatedFormat('d M');
            $pemasukanData30[$date] = 0;
            $pengeluaranData30[$date] = 0;
        }

        $reports30 = FinancialReport::where('report_date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('report_date, type, SUM(amount) as total')
            ->groupBy('report_date', 'type')
            ->get();

        foreach ($reports30 as $r) {
            $dateStr = $r->report_date instanceof Carbon ? $r->report_date->toDateString() : $r->report_date;
            if (isset($pemasukanData30[$dateStr])) {
                if ($r->type === 'pemasukan') {
                    $pemasukanData30[$dateStr] = (float) $r->total;
                } else {
                    $pengeluaranData30[$dateStr] = (float) $r->total;
                }
            }
        }

        $chartLabels30 = array_values($dates30);
        $pemasukanChartData = array_values($pemasukanData30);
        $pengeluaranChartData = array_values($pengeluaranData30);

        // Siapkan data pie chart (distribusi koin saham rekan aktif)
        $rekan = User::where('is_active', true)
            ->orderBy('username')
            ->get(['username', 'coin_saham']);

        $sahamLabels = $rekan->pluck('username')->toArray();
        $sahamValues = $rekan->pluck('coin_saham')->map(fn($v) => (float)$v)->toArray();

        // Cek sisa koin tak terbagi untuk diselipkan di pie chart agar presisi 100%
        $totalTerbagi = array_sum($sahamValues);
        if ($totalTerbagi < 100) {
            $sahamLabels[] = 'Sisa Tersedia';
            $sahamValues[] = 100 - $totalTerbagi;
        }

        return view('rekan.dashboard', compact(
            'poinSaya',
            'coinSaham',
            'estimasiProfit',
            'chartLabels',
            'chartData',
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih',
            'chartLabels30',
            'pemasukanChartData',
            'pengeluaranChartData',
            'sahamLabels',
            'sahamValues'
        ));
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FinancialReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * DashboardController SuperAdmin
 * Menampilkan ringkasan data bisnis: keuangan harian, chart arus kas 30 hari,
 * pie chart saham, dan rekan aktif.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Hitung data metrik
        $totalPemasukan  = (float) FinancialReport::pemasukan()->sum('amount');
        $totalPengeluaran = (float) FinancialReport::pengeluaran()->sum('amount');
        $labaBersih       = $totalPemasukan - $totalPengeluaran;
        $jumlahRekanAktif = User::where('is_active', true)->count();

        // 2. Siapkan data chart garis (arus keuangan 30 hari terakhir)
        $dates = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            // Format label tanggal bahasa Indonesia: "24 Mei"
            $dates[$date] = Carbon::parse($date)->translatedFormat('d M');
            $pemasukanData[$date] = 0;
            $pengeluaranData[$date] = 0;
        }

        $reports = FinancialReport::where('report_date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('report_date, type, SUM(amount) as total')
            ->groupBy('report_date', 'type')
            ->get();

        foreach ($reports as $r) {
            $dateStr = $r->report_date instanceof Carbon ? $r->report_date->toDateString() : $r->report_date;
            if (isset($pemasukanData[$dateStr])) {
                if ($r->type === 'pemasukan') {
                    $pemasukanData[$dateStr] = (float) $r->total;
                } else {
                    $pengeluaranData[$dateStr] = (float) $r->total;
                }
            }
        }

        $chartLabels = array_values($dates);
        $pemasukanChartData = array_values($pemasukanData);
        $pengeluaranChartData = array_values($pengeluaranData);

        // 3. Siapkan data pie chart (distribusi koin saham rekan aktif)
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

        return view('superadmin.dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih',
            'jumlahRekanAktif',
            'chartLabels',
            'pemasukanChartData',
            'pengeluaranChartData',
            'sahamLabels',
            'sahamValues'
        ));
    }
}

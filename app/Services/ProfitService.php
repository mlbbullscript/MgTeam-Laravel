<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\FinancialReport;
use App\Models\User;
use App\Models\ProfitDistribution;
use App\Models\ProfitDistributionDetail;
use Illuminate\Support\Facades\DB;

/**
 * ProfitService — Semua kalkulasi bisnis terkait profit.
 *
 * Formula inti:
 *   Laba Bersih    = Total Pemasukan - Total Pengeluaran
 *   Pool Saham     = Laba Bersih × (pct_saham / 100)
 *   Pool Kerja     = Laba Bersih × (pct_kerja / 100)
 *   Bagian Saham/u = (coin_saham_user / 100) × Pool Saham
 *   Bagian Kerja/u = (poin_kerja_user / total_poin_semua) × Pool Kerja
 */
class ProfitService
{
    public function __construct(
        private readonly PoinKerjaService $poinKerjaService
    ) {}

    /**
     * Hitung laba bersih berdasarkan semua laporan keuangan yang ada.
     */
    public function hitungLabaBersih(): float
    {
        $totalPemasukan  = FinancialReport::pemasukan()->sum('amount');
        $totalPengeluaran = FinancialReport::pengeluaran()->sum('amount');
        return (float) ($totalPemasukan - $totalPengeluaran);
    }

    /**
     * Hitung breakdown profit lengkap per rekan aktif.
     *
     * @return array{
     *   laba_bersih: float,
     *   pct_saham: float,
     *   pct_kerja: float,
     *   pool_saham: float,
     *   pool_kerja: float,
     *   total_poin: float,
     *   rekan: array
     * }
     */
    public function hitungBreakdown(): array
    {
        $labaBersih = $this->hitungLabaBersih();
        $pctSaham   = Setting::pctSaham();
        $pctKerja   = Setting::pctKerja();

        $poolSaham = $labaBersih * ($pctSaham / 100);
        $poolKerja = $labaBersih * ($pctKerja / 100);

        // Ambil semua poin kerja sekaligus (efisien — 2 query)
        $semuaPoin = $this->poinKerjaService->hitungSemuaUser();
        $totalPoin = (float) array_sum($semuaPoin);

        // Hitung per rekan
        $rekan = User::where('is_active', true)->get();

        $breakdown = [];
        foreach ($rekan as $r) {
            $poinUser    = $semuaPoin[$r->id] ?? 0;
            $bagianSaham = ($r->coin_saham / 100) * $poolSaham;
            $bagianKerja = $totalPoin > 0
                ? ($poinUser / $totalPoin) * $poolKerja
                : 0;

            $breakdown[] = [
                'user'         => $r,
                'coin_saham'   => (float) $r->coin_saham,
                'poin_kerja'   => (float) $poinUser,
                'bagian_saham' => $bagianSaham,
                'bagian_kerja' => $bagianKerja,
                'total'        => $bagianSaham + $bagianKerja,
            ];
        }

        return [
            'laba_bersih'  => $labaBersih,
            'pct_saham'    => $pctSaham,
            'pct_kerja'    => $pctKerja,
            'pool_saham'   => $poolSaham,
            'pool_kerja'   => $poolKerja,
            'total_poin'   => $totalPoin,
            'rekan'        => $breakdown,
        ];
    }

    /**
     * Simpan distribusi profit ke database.
     * Dipanggil oleh SuperAdmin saat menekan tombol "Distribusikan".
     */
    public function distribusikan(int $distributedBy, ?string $catatan = null): ProfitDistribution
    {
        $data = $this->hitungBreakdown();

        return DB::transaction(function () use ($data, $distributedBy, $catatan) {
            // Buat record distribusi induk
            $distribusi = ProfitDistribution::create([
                'distributed_by'   => $distributedBy,
                'laba_bersih'      => $data['laba_bersih'],
                'pct_saham_used'   => $data['pct_saham'],
                'pct_kerja_used'   => $data['pct_kerja'],
                'total_pool_saham' => $data['pool_saham'],
                'total_pool_kerja' => $data['pool_kerja'],
                'notes'            => $catatan,
                'distributed_at'   => now(),
            ]);

            // Buat detail per rekan
            foreach ($data['rekan'] as $item) {
                ProfitDistributionDetail::create([
                    'distribution_id' => $distribusi->id,
                    'user_id'         => $item['user']->id,
                    'coin_saham'      => $item['coin_saham'],
                    'poin_kerja'      => $item['poin_kerja'],
                    'bagian_saham'    => $item['bagian_saham'],
                    'bagian_kerja'    => $item['bagian_kerja'],
                    'total'           => $item['total'],
                    'status'          => 'pending',
                ]);
            }

            return $distribusi;
        });
    }
}

<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Services\ProfitService;
use App\Services\PoinKerjaService;
use App\Models\Setting;

class ProfitCalculatorController extends Controller
{
    public function __construct(
        private readonly ProfitService $profitService,
        private readonly PoinKerjaService $poinKerjaService
    ) {}

    public function index()
    {
        $data      = $this->profitService->hitungBreakdown();
        $poinSaya  = $this->poinKerjaService->hitungPoinUser(auth()->id());
        $userSaya  = auth()->user();

        // Cari bagian milik user yang login
        $bagianSaya = collect($data['rekan'])
            ->firstWhere(fn($r) => $r['user']->id === auth()->id());

        return view('rekan.kalkulator', compact('data', 'poinSaya', 'userSaya', 'bagianSaya'));
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\ProfitService;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    public function __construct(private readonly ProfitService $profitService) {}

    public function index()
    {
        $data    = $this->profitService->hitungBreakdown();
        $setting = ['pct_saham' => Setting::pctSaham(), 'pct_kerja' => Setting::pctKerja()];
        return view('superadmin.profit.index', compact('data', 'setting'));
    }

    public function distribusi(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ], ['catatan.max' => 'Catatan maksimal 500 karakter.']);

        $distribusi = $this->profitService->distribusikan(auth()->id(), $request->input('catatan'));

        return redirect()->route('superadmin.profit.index')
            ->with('success', "Distribusi profit berhasil dicatat. ID: #{$distribusi->id}");
    }
}

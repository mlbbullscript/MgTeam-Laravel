<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Models\ProfitDistributionDetail;

class PersonalIncomeController extends Controller
{
    public function index()
    {
        $riwayat = ProfitDistributionDetail::with('distribusi.distributor')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(15);

        $totalDiterima = ProfitDistributionDetail::where('user_id', auth()->id())
            ->where('status', 'ditransfer')
            ->sum('total');

        return view('rekan.penghasilan', compact('riwayat', 'totalDiterima'));
    }
}

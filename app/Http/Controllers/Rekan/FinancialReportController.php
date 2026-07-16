<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFinancialReportRequest;
use App\Models\FinancialReport;
use Illuminate\Http\Request;
use App\Traits\ImageCompressionTrait;

class FinancialReportController extends Controller
{
    use ImageCompressionTrait;

    public function index(Request $request)
    {
        $filter = $request->input('filter', 'bulan_ini');
        [$dari, $sampai] = $this->rentangTanggal($filter, $request);

        $laporan = FinancialReport::with('pembuat')
            ->tanggal($dari, $sampai)
            ->orderByDesc('report_date')
            ->paginate(20)
            ->withQueryString();

        $totalPemasukan   = FinancialReport::pemasukan()->tanggal($dari, $sampai)->sum('amount');
        $totalPengeluaran = FinancialReport::pengeluaran()->tanggal($dari, $sampai)->sum('amount');

        return view('rekan.laporan.index', compact('laporan', 'filter', 'dari', 'sampai', 'totalPemasukan', 'totalPengeluaran'));
    }

    public function create()
    {
        return view('rekan.laporan.create');
    }

    public function store(StoreFinancialReportRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $data['screenshot'] = $this->compressAndEncodeToBase64($file);
        }

        FinancialReport::create($data);

        return redirect()->route('rekan.laporan.index')
            ->with('success', 'Laporan berhasil diunggah.');
    }

    private function rentangTanggal(string $filter, Request $request): array
    {
        return match ($filter) {
            'hari_ini'   => [today()->toDateString(), today()->toDateString()],
            'minggu_ini' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'custom'     => [
                $request->input('dari', now()->startOfMonth()->toDateString()),
                $request->input('sampai', now()->toDateString()),
            ],
            default      => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }
}

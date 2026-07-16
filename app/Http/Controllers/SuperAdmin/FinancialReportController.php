<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFinancialReportRequest;
use App\Models\FinancialReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
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
            ->paginate(25)
            ->withQueryString();

        $totalPemasukan  = FinancialReport::pemasukan()->tanggal($dari, $sampai)->sum('amount');
        $totalPengeluaran = FinancialReport::pengeluaran()->tanggal($dari, $sampai)->sum('amount');

        return view('superadmin.laporan.index', compact(
            'laporan', 'filter', 'dari', 'sampai', 'totalPemasukan', 'totalPengeluaran'
        ));
    }

    public function create()
    {
        return view('superadmin.laporan.create');
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

        return redirect()->route('superadmin.laporan.index')
            ->with('success', 'Laporan keuangan berhasil ditambahkan.');
    }

    public function show($id) { return redirect()->route('superadmin.laporan.edit', $id); }

    public function edit(FinancialReport $laporan)
    {
        return view('superadmin.laporan.edit', compact('laporan'));
    }

    public function update(StoreFinancialReportRequest $request, FinancialReport $laporan)
    {
        $data = $request->validated();

        if ($request->hasFile('screenshot')) {
            if ($laporan->screenshot && !str_starts_with($laporan->screenshot, 'data:')) {
                Storage::disk('public')->delete($laporan->screenshot);
            }
            $file = $request->file('screenshot');
            $data['screenshot'] = $this->compressAndEncodeToBase64($file);
        }

        $laporan->update($data);

        return redirect()->route('superadmin.laporan.index')
            ->with('success', 'Laporan keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialReport $laporan)
    {
        $laporan->delete(); // soft delete
        return redirect()->route('superadmin.laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /** Tentukan rentang tanggal berdasarkan filter yang dipilih */
    private function rentangTanggal(string $filter, Request $request): array
    {
        return match ($filter) {
            'hari_ini'   => [today()->toDateString(), today()->toDateString()],
            'minggu_ini' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'custom'     => [
                $request->input('dari', now()->startOfMonth()->toDateString()),
                $request->input('sampai', now()->toDateString()),
            ],
            default      => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()], // bulan_ini
        };
    }
}

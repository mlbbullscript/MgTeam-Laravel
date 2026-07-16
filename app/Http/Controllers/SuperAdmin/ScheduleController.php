<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private array $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwal = Schedule::with(['assignee', 'pembuat'])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Build weekly schedule table: keyed by hari -> array of max 2 schedules
        $semuaJadwal = Schedule::with('assignee')->get();
        $jadwalPerHari = [];
        foreach ($this->daftarHari as $hari) {
            $jadwalPerHari[$hari] = $semuaJadwal->where('hari', $hari)->values();
        }

        $daftarHari = $this->daftarHari;

        return view('superadmin.jadwal.index', compact('jadwal', 'jadwalPerHari', 'daftarHari'));
    }

    public function create()
    {
        $rekan = User::where('is_active', true)->orderBy('username')->get();
        $slotPenuh = Schedule::select('hari')
            ->groupBy('hari')
            ->havingRaw('COUNT(*) >= 2')
            ->pluck('hari')
            ->toArray();

        return view('superadmin.jadwal.create', compact('rekan', 'slotPenuh'));
    }

    public function store(StoreScheduleRequest $request)
    {
        $data = $request->validated();

        // Aturan: max 2 orang per hari
        $count = Schedule::where('hari', $data['hari'])->count();
        if ($count >= 2) {
            return back()
                ->withInput()
                ->with('error', 'Hari ' . $data['hari'] . ' sudah penuh (maksimal 2 orang per hari). Pilih hari lain.');
        }

        $data['created_by'] = auth()->id();
        Schedule::create($data);

        return redirect()->route('superadmin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show($id) { return redirect()->route('superadmin.jadwal.edit', $id); }

    public function edit(Schedule $jadwal)
    {
        $rekan = User::where('is_active', true)->orderBy('username')->get();
        $slotPenuh = Schedule::select('hari')
            ->where('id', '!=', $jadwal->id)
            ->groupBy('hari')
            ->havingRaw('COUNT(*) >= 2')
            ->pluck('hari')
            ->toArray();

        return view('superadmin.jadwal.edit', compact('jadwal', 'rekan', 'slotPenuh'));
    }

    public function update(StoreScheduleRequest $request, Schedule $jadwal)
    {
        $data = $request->validated();

        // Aturan: max 2 orang per hari (kecuali jadwal ini sendiri)
        $count = Schedule::where('hari', $data['hari'])
            ->where('id', '!=', $jadwal->id)
            ->count();
        if ($count >= 2) {
            return back()
                ->withInput()
                ->with('error', 'Hari ' . $data['hari'] . ' sudah penuh (maksimal 2 orang per hari). Pilih hari lain.');
        }

        $jadwal->update($data);
        return redirect()->route('superadmin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('superadmin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}

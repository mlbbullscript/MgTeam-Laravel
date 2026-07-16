<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    private array $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwal = Schedule::with('pembuat')
            ->where('assigned_to', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Build weekly schedule table (full — semua orang)
        $semuaJadwal = Schedule::with('assignee')->get();
        $jadwalPerHari = [];
        foreach ($this->daftarHari as $hari) {
            $jadwalPerHari[$hari] = $semuaJadwal->where('hari', $hari)->values();
        }

        $daftarHari = $this->daftarHari;

        return view('rekan.jadwal.index', compact('jadwal', 'jadwalPerHari', 'daftarHari'));
    }
}

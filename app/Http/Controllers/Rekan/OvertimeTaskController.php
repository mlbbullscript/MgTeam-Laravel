<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use App\Models\OvertimeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OvertimeTaskController extends Controller
{
    public function index()
    {
        $tersedia = OvertimeTask::tersedia()->orderByDesc('created_at')->get();
        $diambilku = OvertimeTask::diambilOleh(auth()->id())->orderByDesc('taken_at')->get();
        return view('rekan.lembur.index', compact('tersedia', 'diambilku'));
    }

    /**
     * Ambil tugas lembur — First Come First Served dengan proteksi race condition.
     * Menggunakan DB::transaction + lockForUpdate() sesuai ARCHITECTURE.md.
     */
    public function ambil(Request $request, $overtimeTask)
    {
        try {
            DB::transaction(function () use ($overtimeTask) {
                // lockForUpdate() memastikan hanya satu request yang bisa mengubah baris ini
                $lembur = OvertimeTask::lockForUpdate()->findOrFail($overtimeTask);

                if ($lembur->status === 'diambil') {
                    throw new \Exception('Tugas lembur ini sudah diambil oleh rekan lain.');
                }

                $lembur->update([
                    'status'   => 'diambil',
                    'taken_by' => auth()->id(),
                    'taken_at' => now(),
                ]);
            });

            return redirect()->route('rekan.lembur.index')
                ->with('success', 'Tugas lembur berhasil diambil! Poin kerja akan bertambah.');

        } catch (\Exception $e) {
            return redirect()->route('rekan.lembur.index')
                ->with('error', $e->getMessage());
        }
    }
}

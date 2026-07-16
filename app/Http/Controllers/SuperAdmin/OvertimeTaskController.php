<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOvertimeTaskRequest;
use App\Models\OvertimeTask;

class OvertimeTaskController extends Controller
{
    public function index()
    {
        $lembur = OvertimeTask::with(['pengambil', 'pembuat'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('superadmin.lembur.index', compact('lembur'));
    }

    public function create()
    {
        return view('superadmin.lembur.create');
    }

    public function store(StoreOvertimeTaskRequest $request)
    {
        OvertimeTask::create(array_merge(
            $request->validated(),
            ['created_by' => auth()->id()]
        ));

        return redirect()->route('superadmin.lembur.index')
            ->with('success', 'Tugas lembur berhasil ditambahkan.');
    }

    public function show($id) { return redirect()->route('superadmin.lembur.edit', $id); }

    public function edit(OvertimeTask $lembur)
    {
        return view('superadmin.lembur.edit', compact('lembur'));
    }

    public function update(StoreOvertimeTaskRequest $request, OvertimeTask $lembur)
    {
        // Jangan ubah status/taken_by/taken_at lewat form ini
        $lembur->update($request->only(['name', 'description', 'poin_kerja']));
        return redirect()->route('superadmin.lembur.index')
            ->with('success', 'Tugas lembur berhasil diperbarui.');
    }

    public function destroy(OvertimeTask $lembur)
    {
        $lembur->delete();
        return redirect()->route('superadmin.lembur.index')
            ->with('success', 'Tugas lembur berhasil dihapus.');
    }

    /**
     * Ambil tugas lembur oleh Admin (SuperAdmin)
     */
    public function ambil(\Illuminate\Http\Request $request, $overtimeTask)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($overtimeTask) {
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

            return redirect()->route('superadmin.lembur.index')
                ->with('success', 'Tugas lembur berhasil diambil! Poin kerja Anda akan bertambah.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.lembur.index')
                ->with('error', $e->getMessage());
        }
    }
}

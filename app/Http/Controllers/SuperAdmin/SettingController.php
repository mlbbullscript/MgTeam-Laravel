<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function toggleIzinUpload(Request $request)
    {
        $saat_ini = Setting::izinUploadRekan();
        Setting::setValue('izin_upload_rekan', $saat_ini ? 'false' : 'true');
        $status = !$saat_ini ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Izin upload laporan oleh Rekan telah {$status}.");
    }

    public function updateAlokasi(Request $request)
    {
        $request->validate([
            'pct_saham' => ['required', 'numeric', 'min:0', 'max:100'],
            'pct_kerja' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'pct_saham.required' => 'Persentase saham wajib diisi.',
            'pct_kerja.required' => 'Persentase kerja wajib diisi.',
        ]);

        $pctSaham = (float) $request->input('pct_saham');
        $pctKerja = (float) $request->input('pct_kerja');

        if (abs(($pctSaham + $pctKerja) - 100) > 0.01) {
            return back()->withErrors(['pct_saham' => 'Total persentase saham + kerja harus = 100%.']);
        }

        Setting::setValue('pct_saham', (string) $pctSaham);
        Setting::setValue('pct_kerja', (string) $pctKerja);

        return back()->with('success', "Alokasi diperbarui: Saham {$pctSaham}% / Kerja {$pctKerja}%.");
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSahamRequest;
use App\Models\User;
use App\Services\PoinKerjaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\ImageCompressionTrait;

class RekanController extends Controller
{
    use ImageCompressionTrait;

    public function __construct(private readonly PoinKerjaService $poinKerjaService) {}

    /** Daftar semua rekan beserta saham dan poin kerja mereka */
    public function index()
    {
        $rekan    = User::orderBy('username')->get();
        $semuaPoin = $this->poinKerjaService->hitungSemuaUser();
        $totalKoin = $rekan->sum('coin_saham');

        return view('superadmin.rekan.index', compact('rekan', 'semuaPoin', 'totalKoin'));
    }

    public function create()
    {
        $sisaKoin = 100 - User::sum('coin_saham');
        return view('superadmin.rekan.create', compact('sisaKoin'));
    }

    public function store(UpdateSahamRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'username'   => $data['username'],
            'password'   => Hash::make($data['password']),
            'coin_saham' => $data['coin_saham'],
            'role'       => 'rekan',
            'is_active'  => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('photo_profile')) {
            $file = $request->file('photo_profile');
            $base64 = $this->compressAndEncodeToBase64($file, 400); // 400px width limit for profile photo
            $user->update(['photo_profile' => $base64]);
        }

        return redirect()->route('superadmin.rekan.index')
            ->with('success', "Rekan {$user->username} berhasil ditambahkan.");
    }

    public function show($id) { return redirect()->route('superadmin.rekan.edit', $id); }

    public function edit(User $rekan)
    {
        $sisaKoin = 100 - User::where('id', '!=', $rekan->id)->sum('coin_saham');
        return view('superadmin.rekan.edit', compact('rekan', 'sisaKoin'));
    }

    public function update(UpdateSahamRequest $request, User $rekan)
    {
        $data = $request->validated();

        $updateData = [
            'username'   => $data['username'],
            'coin_saham' => $data['coin_saham'],
            'is_active'  => $request->boolean('is_active', true),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('photo_profile')) {
            // Hapus foto lama jika ada dan bukan base64
            if ($rekan->photo_profile && !str_starts_with($rekan->photo_profile, 'data:')) {
                Storage::disk('public')->delete($rekan->photo_profile);
            }
            $file = $request->file('photo_profile');
            $updateData['photo_profile'] = $this->compressAndEncodeToBase64($file, 400); // 400px width limit for profile photo
        }

        $rekan->update($updateData);

        return redirect()->route('superadmin.rekan.index')
            ->with('success', "Data rekan {$rekan->username} berhasil diperbarui.");
    }

    public function destroy(User $rekan)
    {
        abort_if($rekan->id === auth()->id(), 403, 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        
        // Nonaktifkan saja, tidak hapus permanen (agar riwayat tetap ada)
        $rekan->update(['is_active' => false]);

        return redirect()->route('superadmin.rekan.index')
            ->with('success', "Rekan {$rekan->username} telah dinonaktifkan.");
    }

    /** Edit koin saham rekan (tambah atau kurang), termasuk superadmin sendiri */
    public function editKoin(Request $request, User $rekan)
    {
        $request->validate([
            'tipe'   => ['required', 'in:tambah,kurang'],
            'jumlah' => ['required', 'numeric', 'min:0.01', 'max:100'],
        ], [
            'tipe.required'   => 'Tipe perubahan wajib dipilih.',
            'tipe.in'         => 'Tipe hanya boleh "tambah" atau "kurang".',
            'jumlah.required' => 'Jumlah koin wajib diisi.',
            'jumlah.min'      => 'Jumlah koin minimal 0.01.',
            'jumlah.numeric'  => 'Jumlah koin harus berupa angka.',
        ]);

        $jumlah        = (float) $request->jumlah;
        $koinSekarang  = (float) $rekan->coin_saham;

        if ($request->tipe === 'tambah') {
            // Validasi: total seluruh koin tidak boleh melebihi 100
            $totalKoinLain = (float) User::where('id', '!=', $rekan->id)->sum('coin_saham');
            $sisaKoin      = 100 - $totalKoinLain - $koinSekarang;

            if ($jumlah > $sisaKoin) {
                return back()->with('error', "Tidak dapat menambah koin. Sisa koin yang tersedia hanya " . number_format($sisaKoin, 2) . " koin.");
            }

            $koinBaru = $koinSekarang + $jumlah;
        } else {
            // Validasi: koin tidak boleh di bawah 0
            if ($jumlah > $koinSekarang) {
                return back()->with('error', "Tidak dapat mengurangi koin. Koin saat ini hanya " . number_format($koinSekarang, 2) . " koin.");
            }

            $koinBaru = $koinSekarang - $jumlah;
        }

        $rekan->update(['coin_saham' => $koinBaru]);

        $aksi = $request->tipe === 'tambah' ? 'ditambahkan' : 'dikurangi';
        return redirect()->route('superadmin.rekan.index')
            ->with('success', "Koin saham {$rekan->username} berhasil {$aksi} sebesar " . number_format($jumlah, 2) . " koin. Total koin sekarang: " . number_format($koinBaru, 2) . "%.");
    }

    /** Edit poin kerja rekan (tambah atau kurang), termasuk superadmin sendiri */
    public function editPoin(Request $request, User $rekan)
    {
        $request->validate([
            'tipe'   => ['required', 'in:tambah,kurang'],
            'jumlah' => ['required', 'numeric', 'min:0.01', 'max:10000'],
        ], [
            'tipe.required'   => 'Tipe perubahan wajib dipilih.',
            'tipe.in'         => 'Tipe hanya boleh "tambah" atau "kurang".',
            'jumlah.required' => 'Jumlah poin wajib diisi.',
            'jumlah.min'      => 'Jumlah poin minimal 0.01.',
            'jumlah.numeric'  => 'Jumlah poin harus berupa angka.',
        ]);

        $jumlah        = (float) $request->jumlah;
        $poinSekarang  = (float) $this->poinKerjaService->hitungPoinUser($rekan->id);
        $adjSekarang   = (float) $rekan->manual_points_adjustment;

        if ($request->tipe === 'tambah') {
            $adjBaru = $adjSekarang + $jumlah;
            $poinBaru = $poinSekarang + $jumlah;
        } else {
            // Validasi: total poin tidak boleh di bawah 0
            if ($jumlah > $poinSekarang) {
                return back()->with('error', "Tidak dapat mengurangi poin. Poin saat ini hanya " . number_format($poinSekarang, 2) . " poin.");
            }

            $adjBaru = $adjSekarang - $jumlah;
            $poinBaru = $poinSekarang - $jumlah;
        }

        $rekan->update(['manual_points_adjustment' => $adjBaru]);

        $aksi = $request->tipe === 'tambah' ? 'ditambahkan' : 'dikurangi';
        return redirect()->route('superadmin.rekan.index')
            ->with('success', "Poin kerja {$rekan->username} berhasil {$aksi} sebesar " . number_format($jumlah, 2) . " poin. Total poin sekarang: " . number_format($poinBaru, 2) . " Poin.");
    }
}

<?php

namespace App\Http\Controllers\Rekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Traits\ImageCompressionTrait;

class ProfileController extends Controller
{
    use ImageCompressionTrait;

    public function index()
    {
        $user = auth()->user();
        return view('rekan.profil', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $request->validate([
            'username'      => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            'photo_profile' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan oleh rekan lain.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'photo_profile.mimes' => 'Format foto harus berupa JPG atau PNG.',
            'photo_profile.max'   => 'Ukuran foto maksimal 2MB.',
        ]);

        $updateData = [
            'username' => $request->input('username'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        if ($request->hasFile('photo_profile')) {
            if ($user->photo_profile && !str_starts_with($user->photo_profile, 'data:')) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $file = $request->file('photo_profile');
            $updateData['photo_profile'] = $this->compressAndEncodeToBase64($file, 400); // Koin profil cukup lebar 400px (sangat ringan)
        }

        $user->update($updateData);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}

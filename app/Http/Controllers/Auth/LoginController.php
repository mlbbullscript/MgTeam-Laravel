<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * LoginController — Menangani autentikasi sesi pengguna
 *
 * Menggunakan username (bukan email) untuk login.
 * Setelah login, redirect ke dashboard sesuai role user.
 */
class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Jika sudah login, redirect ke dashboard yang sesuai.
     */
    public function tampilLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBerdasarkanRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login — validasi credentials dan buat sesi.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba autentikasi — password diperiksa otomatis oleh Auth guard
        $berhasil = Auth::attempt([
            'username'  => $data['username'],
            'password'  => $data['password'],
            'is_active' => true,    // hanya user aktif yang bisa login
        ], $request->boolean('ingat_saya'));

        if (!$berhasil) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password tidak valid, atau akun tidak aktif.',
            ]);
        }

        // Regenerasi session ID untuk mencegah session fixation attack
        $request->session()->regenerate();

        return $this->redirectBerdasarkanRole();
    }

    /**
     * Proses logout — hapus sesi dan redirect ke halaman login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Helper — redirect ke dashboard berdasarkan role user yang sedang login.
     */
    private function redirectBerdasarkanRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->route('rekan.dashboard');
    }
}

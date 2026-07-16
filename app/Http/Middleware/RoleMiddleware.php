<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RoleMiddleware
 *
 * Memproteksi route berdasarkan role pengguna.
 * Redirect otomatis ke dashboard yang sesuai jika role tidak cocok.
 *
 * Cara penggunaan di routes/web.php:
 *   Route::middleware(['auth', 'role:superadmin'])->group(...)
 *   Route::middleware(['auth', 'role:rekan'])->group(...)
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek apakah role user sesuai dengan yang dibutuhkan route
        if ($user->role !== $role) {
            // Redirect ke dashboard yang sesuai role mereka
            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            return redirect()->route('rekan.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

/**
 * Middleware IzinUploadMiddleware
 *
 * Memproteksi route upload laporan keuangan oleh Rekan.
 * Cek setting 'izin_upload_rekan' di database sebelum mengizinkan akses.
 *
 * Cara penggunaan di routes/web.php:
 *   Route::post('/rekan/laporan-keuangan', [...])->middleware('izin-upload');
 */
class IzinUploadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah SuperAdmin mengizinkan rekan untuk upload laporan
        if (!Setting::izinUploadRekan()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'pesan' => 'Upload laporan oleh Rekan sedang tidak diizinkan oleh SuperAdmin.',
                ], 403);
            }

            return redirect()->route('rekan.laporan.index')
                ->with('error', 'Upload laporan sedang tidak diizinkan oleh SuperAdmin.');
        }

        return $next($request);
    }
}

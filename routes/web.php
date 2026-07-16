<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\Rekan\DashboardController as RekanDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes — Bisnis Manajemen Tim
|--------------------------------------------------------------------------
|
| Semua route diorganisir berdasarkan role:
|   - Guest: hanya halaman login
|   - SuperAdmin: prefix /superadmin, middleware auth + role:superadmin
|   - Rekan: prefix /rekan, middleware auth + role:rekan
|
*/

// =====================================================================
// REDIRECT ROOT — Arahkan ke login jika belum login
// =====================================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// =====================================================================
// AUTH ROUTES — Guest only (sudah login → redirect ke dashboard)
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'tampilLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.proses');
});

// Logout tersedia untuk semua user yang sudah login
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// =====================================================================
// SUPERADMIN ROUTES — Middleware: auth + role:superadmin
// =====================================================================
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])
            ->name('dashboard');

        // Kelola Rekan
        Route::resource('rekan', \App\Http\Controllers\SuperAdmin\RekanController::class)
            ->names('rekan');
        Route::post('/rekan/{rekan}/edit-koin', [\App\Http\Controllers\SuperAdmin\RekanController::class, 'editKoin'])
            ->name('rekan.edit-koin');
        Route::post('/rekan/{rekan}/edit-poin', [\App\Http\Controllers\SuperAdmin\RekanController::class, 'editPoin'])
            ->name('rekan.edit-poin');

        // Jadwal Kerja
        Route::resource('jadwal', \App\Http\Controllers\SuperAdmin\ScheduleController::class)
            ->names('jadwal');

        // Tugas Lembur
        Route::resource('lembur', \App\Http\Controllers\SuperAdmin\OvertimeTaskController::class)
            ->names('lembur');
        Route::post('/lembur/{overtimeTask}/ambil', [\App\Http\Controllers\SuperAdmin\OvertimeTaskController::class, 'ambil'])
            ->name('lembur.ambil');

        // Laporan Keuangan
        Route::resource('laporan', \App\Http\Controllers\SuperAdmin\FinancialReportController::class)
            ->names('laporan');

        // Distribusi Profit
        Route::get('/profit', [\App\Http\Controllers\SuperAdmin\ProfitController::class, 'index'])
            ->name('profit.index');
        Route::post('/profit/distribusi', [\App\Http\Controllers\SuperAdmin\ProfitController::class, 'distribusi'])
            ->name('profit.distribusi');

        // Pengaturan
        Route::patch('/settings/izin-upload', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'toggleIzinUpload'])
            ->name('settings.izin-upload');
        Route::patch('/settings/alokasi', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'updateAlokasi'])
            ->name('settings.alokasi');
    });

// =====================================================================
// REKAN ROUTES — Middleware: auth + role:rekan
// =====================================================================
Route::middleware(['auth', 'role:rekan'])
    ->prefix('rekan')
    ->name('rekan.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [RekanDashboard::class, 'index'])
            ->name('dashboard');

        // Laporan Keuangan
        Route::get('/laporan', [\App\Http\Controllers\Rekan\FinancialReportController::class, 'index'])
            ->name('laporan.index');
        Route::post('/laporan', [\App\Http\Controllers\Rekan\FinancialReportController::class, 'store'])
            ->middleware('izin-upload')
            ->name('laporan.store');
        Route::get('/laporan/buat', [\App\Http\Controllers\Rekan\FinancialReportController::class, 'create'])
            ->middleware('izin-upload')
            ->name('laporan.create');

        // Jadwal Saya
        Route::get('/jadwal', [\App\Http\Controllers\Rekan\ScheduleController::class, 'index'])
            ->name('jadwal.index');

        // Tugas Lembur
        Route::get('/lembur', [\App\Http\Controllers\Rekan\OvertimeTaskController::class, 'index'])
            ->name('lembur.index');
        Route::post('/lembur/{overtimeTask}/ambil', [\App\Http\Controllers\Rekan\OvertimeTaskController::class, 'ambil'])
            ->name('lembur.ambil');

        // Kalkulator Profit
        Route::get('/kalkulator', [\App\Http\Controllers\Rekan\ProfitCalculatorController::class, 'index'])
            ->name('kalkulator.index');

        // Penghasilan Pribadi
        Route::get('/penghasilan', [\App\Http\Controllers\Rekan\PersonalIncomeController::class, 'index'])
            ->name('penghasilan.index');

        // Profil
        Route::get('/profil', [\App\Http\Controllers\Rekan\ProfileController::class, 'index'])
            ->name('profil.index');
        Route::put('/profil', [\App\Http\Controllers\Rekan\ProfileController::class, 'update'])
            ->name('profil.update');
    });

{{--
    Komponen Sidebar — Navigasi berbeda untuk SuperAdmin dan Rekan
    Dipanggil dari layouts/app.blade.php
--}}

@php
    $user = auth()->user();
    $isSuperAdmin = $user->isSuperAdmin();

    // Helper untuk mendeteksi route aktif
    $isActive = fn(string $routePrefix) => request()->is($routePrefix . '*');
@endphp

@if($isSuperAdmin)
    {{-- ==================== NAVIGASI SUPERADMIN ==================== --}}
    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Menu Utama</p>

    <a href="{{ route('superadmin.dashboard') }}"
       id="nav-sa-dashboard"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/dashboard') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Manajemen</p>

    <a href="{{ route('superadmin.rekan.index') }}"
       id="nav-sa-rekan"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/rekan') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Kelola Rekan
    </a>

    <a href="{{ route('superadmin.jadwal.index') }}"
       id="nav-sa-jadwal"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/jadwal') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Jadwal Kerja
    </a>

    <a href="{{ route('superadmin.lembur.index') }}"
       id="nav-sa-lembur"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/lembur') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Tugas Lembur
    </a>

    <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Keuangan</p>

    <a href="{{ route('superadmin.laporan.index') }}"
       id="nav-sa-laporan"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/laporan') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
        </svg>
        Laporan Keuangan
    </a>

    <a href="{{ route('superadmin.profit.index') }}"
       id="nav-sa-profit"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('superadmin/profit') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Distribusi Profit
    </a>

@else
    {{-- ==================== NAVIGASI REKAN ==================== --}}
    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Menu Saya</p>

    <a href="{{ route('rekan.dashboard') }}"
       id="nav-rekan-dashboard"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/dashboard') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('rekan.jadwal.index') }}"
       id="nav-rekan-jadwal"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/jadwal') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Jadwal Saya
    </a>

    <a href="{{ route('rekan.lembur.index') }}"
       id="nav-rekan-lembur"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/lembur') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Tugas Lembur
    </a>

    <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Keuangan</p>

    <a href="{{ route('rekan.laporan.index') }}"
       id="nav-rekan-laporan"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/laporan') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
        </svg>
        Laporan Keuangan
    </a>

    <a href="{{ route('rekan.kalkulator.index') }}"
       id="nav-rekan-kalkulator"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/kalkulator') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Kalkulator Profit
    </a>

    <a href="{{ route('rekan.penghasilan.index') }}"
       id="nav-rekan-penghasilan"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/penghasilan') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Penghasilan Saya
    </a>

    <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Akun</p>

    <a href="{{ route('rekan.profil.index') }}"
       id="nav-rekan-profil"
       class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
              {{ $isActive('rekan/profil') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profil Saya
    </a>
@endif

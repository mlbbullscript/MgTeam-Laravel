@extends('layouts.app')

@section('title', 'Kelola Rekan')

@section('content')
<div
    class="space-y-6"
    x-data="{
        editKoinOpen: false,
        editKoinUrl: '',
        editKoinNama: '',
        editKoinSaat: 0,
        tipe: 'tambah',
        jumlah: '',
        editPoinOpen: false,
        editPoinUrl: '',
        editPoinNama: '',
        editPoinSaat: 0,
        tipePoin: 'tambah',
        jumlahPoin: '',
        isOpen: false,
        actionUrl: '',
        modalMessage: ''
    }"
>
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Rekan Kerja</h1>
            <p class="mt-1 text-sm text-gray-500">Daftar rekan bisnis, alokasi kepemilikan koin saham, dan akumulasi poin kerja.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.rekan.create') }}" 
               id="btn-tambah-rekan"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Rekan Baru
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card 
            title="Total Rekan Aktif" 
            value="{{ $rekan->where('is_active', true)->count() }} Orang" 
            subtext="Dari total {{ $rekan->count() }} terdaftar" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Koin Saham Terdistribusi" 
            value="{{ number_format($totalKoin, 2) }} Koin" 
            subtext="Maksimal batas alokasi: 100 koin" 
            type="{{ $totalKoin >= 95 ? 'warning' : 'success' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Sisa Koin Saham" 
            value="{{ number_format(100 - $totalKoin, 2) }} Koin" 
            subtext="Tersedia untuk rekan baru/tambahan" 
            type="{{ (100 - $totalKoin) <= 5 ? 'danger' : 'info' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Daftar Rekan</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">{{ $rekan->count() }} Terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Rekan</th>
                        <th class="px-6 py-3.5 text-center">Koin Saham</th>
                        <th class="px-6 py-3.5 text-center">Poin Kerja (Bulan Ini)</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rekan as $r)
                        <tr class="hover:bg-gray-50 transition-colors text-sm text-gray-700">
                            {{-- Foto & Username --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-600">
                                        @if($r->photo_profile)
                                            <img src="{{ $r->photo_profile_url }}" 
                                                 alt="Foto profil {{ $r->username }}" 
                                                 class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <span class="text-sm font-semibold text-white uppercase">
                                                {{ substr($r->username, 0, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $r->username }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ $r->role }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Koin Saham --}}
                            <td class="px-6 py-4 text-center font-bold text-gray-900">
                                <div class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full text-xs font-bold">
                                    <span>🪙</span>
                                    <span>{{ number_format($r->coin_saham, 2) }} %</span>
                                </div>
                            </td>

                            {{-- Poin Kerja --}}
                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-semibold">
                                    ⭐ {{ number_format($semuaPoin[$r->id] ?? 0, 1) }} Poin
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if($r->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 flex-wrap sm:flex-nowrap">
                                    <a href="{{ route('superadmin.rekan.edit', $r->id) }}" 
                                       class="inline-flex items-center text-xs font-semibold text-primary-600 hover:text-primary-900 transition-colors"
                                       title="Edit">
                                        Edit
                                    </a>
                                    {{-- Tombol Edit Koin — tersedia untuk SEMUA user termasuk superadmin sendiri --}}
                                    <button 
                                        @click="editKoinOpen = true; editKoinUrl = '{{ route('superadmin.rekan.edit-koin', $r->id) }}'; editKoinNama = '{{ addslashes($r->username) }}'; editKoinSaat = {{ $r->coin_saham }}; tipe = 'tambah'; jumlah = '';"
                                        class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-950 transition-colors"
                                        title="Edit Koin Saham">
                                        🪙 Edit Koin
                                    </button>
                                    {{-- Tombol Edit Poin — tersedia untuk SEMUA user termasuk superadmin sendiri --}}
                                    <button 
                                        @click="editPoinOpen = true; editPoinUrl = '{{ route('superadmin.rekan.edit-poin', $r->id) }}'; editPoinNama = '{{ addslashes($r->username) }}'; editPoinSaat = {{ $semuaPoin[$r->id] ?? 0 }}; tipePoin = 'tambah'; jumlahPoin = '';"
                                        class="inline-flex items-center text-xs font-semibold text-amber-600 hover:text-amber-955 transition-colors"
                                        title="Kurangi/Tambahkan Poin Kerja">
                                        ⭐ Edit Poin
                                    </button>
                                    @if($r->is_active && $r->id !== auth()->id())
                                        <button 
                                            @click="actionUrl = '{{ route('superadmin.rekan.destroy', $r->id) }}'; modalMessage = 'Apakah Anda yakin ingin menonaktifkan rekan {{ addslashes($r->username) }}? Riwayat poin kerja dan koin sahamnya akan dipertahankan namun ia tidak dapat login lagi.'; isOpen = true;"
                                            class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-900 transition-colors"
                                            title="Nonaktifkan">
                                            Nonaktifkan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-700">Belum ada rekan terdaftar</p>
                                <p class="mt-1 text-xs text-gray-500">Silakan tambahkan rekan kerja baru untuk memulai bisnis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== MODAL: EDIT KOIN SAHAM ===== --}}
    <div 
        x-show="editKoinOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @keydown.escape.window="editKoinOpen = false"
        style="display:none;"
    >
        <div 
            x-show="editKoinOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
            @click.stop
        >
            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                        <span class="text-xl">🪙</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kurangi / Tambahkan Koin Saham</h3>
                        <p class="text-xs text-gray-500" x-text="editKoinNama"></p>
                    </div>
                </div>
                <button @click="editKoinOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Info Koin Saat Ini --}}
            <div class="mb-5 bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">Koin saat ini</span>
                <span class="text-sm font-bold text-gray-900" x-text="parseFloat(editKoinSaat).toFixed(2) + '%'"></span>
            </div>

            {{-- Form Edit Koin --}}
            <form :action="editKoinUrl" method="POST" class="space-y-4">
                @csrf

                {{-- Pilih Tipe --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Perubahan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label 
                            class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all"
                            :class="tipe === 'tambah' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                        >
                            <input type="radio" name="tipe" value="tambah" x-model="tipe" class="sr-only">
                            <span class="text-base">➕</span>
                            <span class="text-sm font-semibold">Tambah</span>
                        </label>
                        <label 
                            class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all"
                            :class="tipe === 'kurang' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                        >
                            <input type="radio" name="tipe" value="kurang" x-model="tipe" class="sr-only">
                            <span class="text-base">➖</span>
                            <span class="text-sm font-semibold">Kurangi</span>
                        </label>
                    </div>
                </div>

                {{-- Input Jumlah --}}
                <div>
                    <label for="jumlah-koin-input" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Koin</label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="jumlah-koin-input"
                            name="jumlah"
                            x-model="jumlah"
                            step="0.01"
                            min="0.01"
                            max="100"
                            placeholder="Contoh: 5.00"
                            class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 pr-10 text-gray-900 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">%</span>
                    </div>
                    {{-- Preview hasil --}}
                    <p class="mt-1.5 text-xs text-gray-500">
                        Koin setelah perubahan:&nbsp;
                        <strong
                            :class="tipe === 'tambah' ? 'text-emerald-600' : 'text-rose-600'"
                            x-text="
                                jumlah && !isNaN(jumlah)
                                    ? (tipe === 'tambah'
                                        ? (parseFloat(editKoinSaat) + parseFloat(jumlah)).toFixed(2)
                                        : Math.max(0, parseFloat(editKoinSaat) - parseFloat(jumlah)).toFixed(2))
                                    : parseFloat(editKoinSaat).toFixed(2)
                            "
                        ></strong>%
                    </p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-2">
                    <button 
                        type="button"
                        @click="editKoinOpen = false"
                        class="flex-1 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        id="btn-simpan-koin"
                        class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-colors"
                        :class="tipe === 'tambah' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                        x-text="tipe === 'tambah' ? 'Tambah Koin' : 'Kurangi Koin'"
                    >
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EDIT POIN KERJA ===== --}}
    <div 
        x-show="editPoinOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @keydown.escape.window="editPoinOpen = false"
        style="display:none;"
    >
        <div 
            x-show="editPoinOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
            @click.stop
        >
            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100">
                        <span class="text-xl">⭐</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kurangi / Tambahkan Poin</h3>
                        <p class="text-xs text-gray-500" x-text="editPoinNama"></p>
                    </div>
                </div>
                <button @click="editPoinOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Info Poin Saat Ini --}}
            <div class="mb-5 bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">Poin saat ini</span>
                <span class="text-sm font-bold text-gray-900" x-text="parseFloat(editPoinSaat).toFixed(1) + ' Poin'"></span>
            </div>

            {{-- Form Edit Poin --}}
            <form :action="editPoinUrl" method="POST" class="space-y-4">
                @csrf

                {{-- Pilih Tipe --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Perubahan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label 
                            class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all"
                            :class="tipePoin === 'tambah' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                        >
                            <input type="radio" name="tipe" value="tambah" x-model="tipePoin" class="sr-only">
                            <span class="text-base">➕</span>
                            <span class="text-sm font-semibold">Tambah</span>
                        </label>
                        <label 
                            class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all"
                            :class="tipePoin === 'kurang' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                        >
                            <input type="radio" name="tipe" value="kurang" x-model="tipePoin" class="sr-only">
                            <span class="text-base">➖</span>
                            <span class="text-sm font-semibold">Kurangi</span>
                        </label>
                    </div>
                </div>

                {{-- Input Jumlah --}}
                <div>
                    <label for="jumlah-poin-input" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Poin</label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="jumlah-poin-input"
                            name="jumlah"
                            x-model="jumlahPoin"
                            step="0.01"
                            min="0.01"
                            max="10000"
                            placeholder="Contoh: 10.0"
                            class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 pr-12 text-gray-900 focus:border-amber-500 focus:ring-amber-500 text-sm"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">Poin</span>
                    </div>
                    {{-- Preview hasil --}}
                    <p class="mt-1.5 text-xs text-gray-500">
                        Poin setelah perubahan:&nbsp;
                        <strong
                            :class="tipePoin === 'tambah' ? 'text-amber-600' : 'text-rose-600'"
                            x-text="
                                jumlahPoin && !isNaN(jumlahPoin)
                                    ? (tipePoin === 'tambah'
                                        ? (parseFloat(editPoinSaat) + parseFloat(jumlahPoin)).toFixed(1)
                                        : Math.max(0, parseFloat(editPoinSaat) - parseFloat(jumlahPoin)).toFixed(1))
                                    : parseFloat(editPoinSaat).toFixed(1)
                            "
                        ></strong> Poin
                    </p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-2">
                    <button 
                        type="button"
                        @click="editPoinOpen = false"
                        class="flex-1 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        id="btn-simpan-poin"
                        class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-colors"
                        :class="tipePoin === 'tambah' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-rose-600 hover:bg-rose-700'"
                        x-text="tipePoin === 'tambah' ? 'Tambah Poin' : 'Kurangi Poin'"
                    >
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Nonaktifkan Rekan --}}
    <x-modal-confirm 
        id="form-nonaktifkan"
        method="DELETE"
        title="Konfirmasi Nonaktifkan Rekan"
        confirmText="Ya, Nonaktifkan"
        type="danger"
    />

</div>
@endsection

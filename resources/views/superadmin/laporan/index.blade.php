@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6" x-data="{ isOpen: false, actionUrl: '', modalMessage: '', modalGambarTerbuka: false, srcGambar: '' }">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan Keuangan Bisnis</h1>
            <p class="mt-1 text-sm text-gray-500">Catat pemasukan dan pengeluaran kas operasional secara transparan dan akuntabel.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.laporan.create') }}" 
               id="btn-tambah-laporan"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Transaksi Baru
            </a>
        </div>
    </div>

    {{-- Filter Rentang Tanggal --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form action="{{ route('superadmin.laporan.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Dropdown Filter --}}
                <div x-data="{ localFilter: '{{ $filter }}' }">
                    <label for="filter" class="block text-xs font-bold uppercase tracking-wider text-gray-500">Rentang Waktu</label>
                    <select name="filter" 
                            id="filter" 
                            x-model="localFilter"
                            class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500">
                        <option value="hari_ini">Hari Ini</option>
                        <option value="minggu_ini">Minggu Ini</option>
                        <option value="bulan_ini">Bulan Ini</option>
                        <option value="custom">Kustom (Rentang Tanggal)</option>
                    </select>
                </div>

                {{-- Dari Tanggal --}}
                <div x-show="localFilter === 'custom'" style="display: none;">
                    <label for="dari" class="block text-xs font-bold uppercase tracking-wider text-gray-500">Dari Tanggal</label>
                    <input type="date" 
                           name="dari" 
                           id="dari" 
                           value="{{ $dari }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500">
                </div>

                {{-- Sampai Tanggal --}}
                <div x-show="localFilter === 'custom'" style="display: none;">
                    <label for="sampai" class="block text-xs font-bold uppercase tracking-wider text-gray-500">Sampai Tanggal</label>
                    <input type="date" 
                           name="sampai" 
                           id="sampai" 
                           value="{{ $sampai }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="shrink-0">
                <button type="submit" 
                        class="w-full md:w-auto inline-flex items-center justify-center rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-semibold text-sm px-4 py-2 shadow-sm transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card 
            title="Total Pemasukan" 
            value="Rp {{ number_format($totalPemasukan, 0, ',', '.') }}" 
            subtext="Kas masuk terkumpul" 
            type="success"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Total Pengeluaran" 
            value="Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}" 
            subtext="Kas keluar operasional" 
            type="danger"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0-5a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        @php
            $labaBersih = $totalPemasukan - $totalPengeluaran;
        @endphp
        <x-stat-card 
            title="Laba / Rugi Bersih" 
            value="Rp {{ number_format($labaBersih, 0, ',', '.') }}" 
            subtext="Pemasukan bersih setelah pengeluaran" 
            type="{{ $labaBersih >= 0 ? 'primary' : 'danger' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Daftar Transaksi Keuangan</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">Halaman {{ $laporan->currentPage() }} dari {{ $laporan->lastPage() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Nama Transaksi & Deskripsi</th>
                        <th class="px-6 py-3.5 text-center">Tipe</th>
                        <th class="px-6 py-3.5 text-right">Nominal</th>
                        <th class="px-6 py-3.5">Pengunggah</th>
                        <th class="px-6 py-3.5 text-center">Lampiran</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($laporan as $l)
                        <tr class="hover:bg-gray-50 transition-colors text-sm text-gray-700">
                            {{-- Tanggal --}}
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($l->report_date)->translatedFormat('d F Y') }}
                            </td>
                            
                            {{-- Nama & Deskripsi --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-950">{{ $l->name }}</p>
                                @if($l->description)
                                    <p class="text-xs text-gray-500 mt-1 max-w-sm line-clamp-2" title="{{ $l->description }}">
                                        {{ $l->description }}
                                    </p>
                                @endif
                            </td>

                            {{-- Tipe --}}
                            <td class="px-6 py-4 text-center">
                                @if($l->type === 'pemasukan')
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>

                            {{-- Nominal --}}
                            <td class="px-6 py-4 text-right font-bold {{ $l->type === 'pemasukan' ? 'text-green-600' : 'text-red-600' }} whitespace-nowrap">
                                {{ $l->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($l->amount, 0, ',', '.') }}
                            </td>

                            {{-- Pembuat --}}
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                <span class="capitalize">{{ $l->pembuat->username }}</span>
                            </td>

                            {{-- Lampiran Screenshot --}}
                            <td class="px-6 py-4 text-center">
                                @if($l->screenshot)
                                    <button @click="srcGambar = '{{ $l->screenshot_url }}'; modalGambarTerbuka = true" 
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-800 transition-colors"
                                            title="Lihat bukti screenshot">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('superadmin.laporan.edit', $l->id) }}" 
                                       class="inline-flex items-center text-xs font-semibold text-primary-600 hover:text-primary-900 transition-colors"
                                       title="Edit">
                                        Edit
                                    </a>
                                    <button 
                                        @click="actionUrl = '{{ route('superadmin.laporan.destroy', $l->id) }}'; modalMessage = 'Apakah Anda yakin ingin menghapus catatan transaksi ini?'; isOpen = true;"
                                        class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-900 transition-colors"
                                        title="Hapus">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-700">Belum ada transaksi</p>
                                <p class="mt-1 text-xs text-gray-500">Catat transaksi pemasukan atau pengeluaran pertama untuk memulai pencatatan kas bisnis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($laporan->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $laporan->links() }}
            </div>
        @endif
    </div>

    {{-- Reusable Confirmation Modal --}}
    <x-modal-confirm 
        id="form-hapus-laporan"
        method="DELETE"
        title="Konfirmasi Hapus Transaksi"
        confirmText="Ya, Hapus"
        type="danger"
    />

    {{-- Modal Preview Gambar Lampiran (Mencegah Crash Mobile) --}}
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        x-show="modalGambarTerbuka"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
        @keydown.escape.window="modalGambarTerbuka = false"
    >
        <div 
            class="relative max-w-3xl w-full bg-white rounded-xl overflow-hidden shadow-2xl p-4 flex flex-col items-center"
            @click.away="modalGambarTerbuka = false"
        >
            {{-- Tombol Tutup --}}
            <button 
                @click="modalGambarTerbuka = false" 
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-full p-1.5 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <h3 class="text-sm font-bold text-gray-900 mb-3 select-none">Bukti Lampiran Transaksi</h3>
            
            {{-- Container Gambar --}}
            <div class="w-full max-h-[70vh] overflow-y-auto flex items-center justify-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                <img :src="srcGambar" class="max-w-full max-h-[65vh] object-contain rounded shadow-sm">
            </div>
        </div>
    </div>
</div>
@endsection

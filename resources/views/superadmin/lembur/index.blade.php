@extends('layouts.app')

@section('title', 'Tugas Lembur')

@section('content')
<div class="space-y-6" x-data="{ isOpen: false, actionUrl: '', modalMessage: '' }">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tugas Lembur Rekan</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola daftar tugas lembur tambahan. Rekan kerja dapat mengambil tugas ini dengan sistem siapa cepat dia dapat.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.lembur.create') }}" 
               id="btn-tambah-lembur"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Lembur Baru
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-stat-card 
            title="Lembur Tersedia" 
            value="{{ \App\Models\OvertimeTask::tersedia()->count() }} Tugas" 
            subtext="Dapat segera diambil oleh rekan kerja" 
            type="success"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Lembur Telah Diambil" 
            value="{{ \App\Models\OvertimeTask::where('status', 'diambil')->count() }} Tugas" 
            subtext="Sedang/telah dikerjakan oleh rekan" 
            type="warning"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Daftar Tugas Lembur</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">Halaman {{ $lembur->currentPage() }} dari {{ $lembur->lastPage() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Nama Tugas & Deskripsi</th>
                        <th class="px-6 py-3.5 text-center">Poin Lembur</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5">Pengambil</th>
                        <th class="px-6 py-3.5">Diambil Pada</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($lembur as $l)
                        <tr class="hover:bg-gray-50 transition-colors text-sm text-gray-700">
                            {{-- Nama Tugas --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-950">{{ $l->name }}</p>
                                @if($l->description)
                                    <p class="text-xs text-gray-500 mt-1 max-w-sm line-clamp-2" title="{{ $l->description }}">
                                        {{ $l->description }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 italic mt-0.5">Tidak ada deskripsi.</p>
                                @endif
                                <p class="text-[10px] text-gray-400 mt-1">Dibuat oleh: <span class="capitalize">{{ $l->pembuat->username }}</span></p>
                            </td>

                            {{-- Poin --}}
                            <td class="px-6 py-4 text-center font-bold">
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-semibold">
                                    ⭐ {{ number_format($l->poin_kerja, 1) }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if($l->status === 'tersedia')
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                        Diambil
                                    </span>
                                @endif
                            </td>

                            {{-- Pengambil --}}
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                @if($l->pengambil)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold text-[10px] uppercase">
                                            {{ substr($l->pengambil->username, 0, 2) }}
                                        </div>
                                        <span>{{ $l->pengambil->username }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Belum ada</span>
                                @endif
                            </td>

                            {{-- Diambil Pada --}}
                            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                @if($l->taken_at)
                                    {{ \Carbon\Carbon::parse($l->taken_at)->translatedFormat('d M Y, H:i') }}
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($l->status === 'tersedia')
                                        <button 
                                            @click="$dispatch('open-claim-modal', { url: '{{ route('superadmin.lembur.ambil', $l->id) }}', message: 'Apakah Anda yakin ingin mengambil tugas lembur {{ addslashes($l->name) }}? Rekan lain tidak akan bisa mengambilnya lagi.' })"
                                            class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-900 transition-colors animate-pulse"
                                            title="Ambil Tugas Lembur">
                                            Klaim Tugas
                                        </button>
                                    @endif
                                    <a href="{{ route('superadmin.lembur.edit', $l->id) }}" 
                                       class="inline-flex items-center text-xs font-semibold text-primary-600 hover:text-primary-900 transition-colors"
                                       title="Edit">
                                        Edit
                                    </a>
                                    <button 
                                        @click="actionUrl = '{{ route('superadmin.lembur.destroy', $l->id) }}'; modalMessage = 'Apakah Anda yakin ingin menghapus tugas lembur ini?'; isOpen = true;"
                                        class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-900 transition-colors"
                                        title="Hapus">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-700">Belum ada tugas lembur</p>
                                <p class="mt-1 text-xs text-gray-500">Buat tugas lembur tambahan agar rekan kerja bisa mengklaimnya dan mempercepat pekerjaan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($lembur->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $lembur->links() }}
            </div>
        @endif
    </div>

    {{-- Reusable Confirmation Modal --}}
    <x-modal-confirm 
        id="form-hapus-lembur"
        method="DELETE"
        title="Konfirmasi Hapus Lembur"
        confirmText="Ya, Hapus"
        type="danger"
    />

    {{-- Modal Konfirmasi Ambil Lembur (dengan Alpine scope terisolasi via custom window event) --}}
    <div x-data="{ isOpen: false, actionUrl: '', modalMessage: '' }" @open-claim-modal.window="isOpen = true; actionUrl = $event.detail.url; modalMessage = $event.detail.message">
        <x-modal-confirm 
            id="form-ambil-lembur"
            method="POST"
            title="Ambil Tugas Lembur"
            confirmText="Ya, Ambil Tugas"
            type="success"
        />
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Jadwal Kerja')

@section('content')
<div class="space-y-8" x-data="{ isOpen: false, actionUrl: '', modalMessage: '' }">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Jadwal Kerja Rekan</h1>
            <p class="mt-1 text-sm text-gray-500">Pantau dan atur penugasan harian rekan. Maksimal <strong>2 orang per hari</strong>.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.jadwal.create') }}" 
               id="btn-tambah-jadwal"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Jadwal Baru
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL JADWAL MINGGUAN --}}
    {{-- ============================================================ --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-indigo-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="font-bold text-gray-800 text-sm tracking-wide">📅 Tabel Jadwal Kerja Mingguan</h3>
            </div>
            <span class="text-xs font-semibold bg-primary-100 text-primary-700 px-2.5 py-1 rounded-full">Maks. 2 Orang / Hari</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 w-28">Hari</th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-primary-500"></span>
                                Orang Pertama
                            </span>
                        </th>
                        <th class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                                Orang Kedua
                            </span>
                        </th>
                        <th class="px-5 py-3.5 text-center">Status Slot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($daftarHari as $hari)
                        @php
                            $slots   = $jadwalPerHari[$hari] ?? collect();
                            $slot1   = $slots->get(0);
                            $slot2   = $slots->get(1);
                            $isFull  = $slots->count() >= 2;
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors text-sm {{ $isFull ? '' : 'bg-amber-50/30' }}">
                            {{-- Hari --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center justify-center w-20 rounded-lg py-1.5 text-xs font-bold
                                    {{ $isFull ? 'bg-green-100 text-green-800' : ($slots->count() === 1 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-700') }}">
                                    {{ $hari }}
                                </span>
                            </td>

                            {{-- Slot 1 --}}
                            <td class="px-5 py-4">
                                @if($slot1)
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-bold text-xs uppercase">
                                            {{ substr($slot1->assignee->username, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-xs">{{ $slot1->assignee->username }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 max-w-[180px] truncate" title="{{ $slot1->task_name }}">{{ $slot1->task_name }}</p>
                                        </div>
                                        <span class="ml-auto shrink-0 inline-flex items-center gap-0.5 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            ⭐ {{ number_format($slot1->poin_kerja, 1) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">— Belum ada —</span>
                                @endif
                            </td>

                            {{-- Slot 2 --}}
                            <td class="px-5 py-4">
                                @if($slot2)
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs uppercase">
                                            {{ substr($slot2->assignee->username, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-xs">{{ $slot2->assignee->username }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 max-w-[180px] truncate" title="{{ $slot2->task_name }}">{{ $slot2->task_name }}</p>
                                        </div>
                                        <span class="ml-auto shrink-0 inline-flex items-center gap-0.5 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            ⭐ {{ number_format($slot2->poin_kerja, 1) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">— Belum ada —</span>
                                @endif
                            </td>

                            {{-- Status Slot --}}
                            <td class="px-5 py-4 text-center">
                                @if($isFull)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        ✅ Penuh
                                    </span>
                                @elseif($slots->count() === 1)
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        ⚠️ 1 Slot Tersisa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        🔴 Kosong
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DAFTAR DETAIL JADWAL --}}
    {{-- ============================================================ --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Daftar Penugasan Jadwal</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">Halaman {{ $jadwal->currentPage() }} dari {{ $jadwal->lastPage() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Hari Tugas</th>
                        <th class="px-6 py-3.5">Rekan</th>
                        <th class="px-6 py-3.5">Tugas &amp; Deskripsi</th>
                        <th class="px-6 py-3.5 text-center">Poin Kerja</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jadwal as $j)
                        <tr class="hover:bg-gray-50 transition-colors text-sm text-gray-700">
                            {{-- Hari --}}
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1 rounded-full text-xs font-bold">
                                    📅 Setiap Hari {{ $j->hari }}
                                </span>
                            </td>
                            
                            {{-- Rekan --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-semibold text-xs uppercase">
                                        {{ substr($j->assignee->username, 0, 2) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $j->assignee->username }}</span>
                                </div>
                            </td>

                            {{-- Tugas & Deskripsi --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-950">{{ $j->task_name }}</p>
                                @if($j->description)
                                    <p class="text-xs text-gray-500 mt-1 max-w-md line-clamp-2" title="{{ $j->description }}">
                                        {{ $j->description }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 italic mt-0.5">Tidak ada deskripsi.</p>
                                @endif
                                <p class="text-[10px] text-gray-400 mt-1">Dibuat oleh: <span class="capitalize">{{ $j->pembuat->username }}</span></p>
                            </td>

                            {{-- Poin Kerja --}}
                            <td class="px-6 py-4 text-center font-bold">
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-semibold">
                                    ⭐ {{ number_format($j->poin_kerja, 1) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('superadmin.jadwal.edit', $j->id) }}" 
                                       class="inline-flex items-center text-xs font-semibold text-primary-600 hover:text-primary-900 transition-colors"
                                       title="Edit">
                                        Edit
                                    </a>
                                    <button 
                                        @click="actionUrl = '{{ route('superadmin.jadwal.destroy', $j->id) }}'; modalMessage = 'Apakah Anda yakin ingin menghapus jadwal ini? Poin rekan yang bersangkutan akan terpengaruh jika poin belum dikunci.'; isOpen = true;"
                                        class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-900 transition-colors"
                                        title="Hapus">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-700">Belum ada jadwal kerja</p>
                                <p class="mt-1 text-xs text-gray-500">Mulai buat jadwal kerja baru untuk mendistribusikan tugas kepada rekan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($jadwal->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $jadwal->links() }}
            </div>
        @endif
    </div>

    {{-- Reusable Confirmation Modal --}}
    <x-modal-confirm 
        id="form-hapus-jadwal"
        method="DELETE"
        title="Konfirmasi Hapus Jadwal"
        confirmText="Ya, Hapus"
        type="danger"
    />
</div>
@endsection

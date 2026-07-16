@extends('layouts.app')

@section('title', 'Jadwal Saya')

@section('content')
<div class="space-y-8">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Jadwal Tugas Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Lihat jadwal kerja mingguan tim dan tugas yang ditugaskan untuk Anda.</p>
    </div>

    {{-- Stats Card --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
            $totalPoinJadwal = $jadwal->sum('poin_kerja');
        @endphp
        <x-stat-card 
            title="Total Poin dari Jadwal Halaman Ini" 
            value="{{ number_format($totalPoinJadwal, 1) }} Poin" 
            subtext="Akumulasi dari {{ $jadwal->count() }} tugas terjadwal" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Koin Saham Anda" 
            value="{{ number_format(auth()->user()->coin_saham, 2) }} %" 
            subtext="Proporsi dividen profit Anda" 
            type="success"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL JADWAL MINGGUAN (FULL TEAM) --}}
    {{-- ============================================================ --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-indigo-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="font-bold text-gray-800 text-sm tracking-wide">📅 Tabel Jadwal Kerja Mingguan Tim</h3>
            </div>
            <span class="text-xs font-semibold bg-primary-100 text-primary-700 px-2.5 py-1 rounded-full">2 Orang / Hari</span>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($daftarHari as $hari)
                        @php
                            $slots  = $jadwalPerHari[$hari] ?? collect();
                            $slot1  = $slots->get(0);
                            $slot2  = $slots->get(1);
                            $myId   = auth()->id();
                            $isMyDay = $slots->contains('assigned_to', $myId);
                        @endphp
                        <tr class="transition-colors text-sm {{ $isMyDay ? 'bg-primary-50/60 hover:bg-primary-50' : 'hover:bg-gray-50/70' }}">
                            {{-- Hari --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-20 rounded-lg py-1.5 text-xs font-bold
                                        {{ $isMyDay ? 'bg-primary-600 text-white shadow-sm' : ($slots->count() >= 2 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $hari }}
                                    </span>
                                    @if($isMyDay)
                                        <span class="text-[10px] font-bold text-primary-600 bg-primary-100 px-1.5 py-0.5 rounded-full">Saya</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Slot 1 --}}
                            <td class="px-5 py-4">
                                @if($slot1)
                                    @php $isMine1 = $slot1->assigned_to === $myId; @endphp
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full font-bold text-xs uppercase
                                            {{ $isMine1 ? 'bg-primary-600 text-white ring-2 ring-primary-300' : 'bg-primary-100 text-primary-700' }}">
                                            {{ substr($slot1->assignee->username, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-xs {{ $isMine1 ? 'text-primary-700' : 'text-gray-900' }}">
                                                {{ $slot1->assignee->username }}
                                                @if($isMine1) <span class="text-[9px] font-bold text-primary-500">(Anda)</span> @endif
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 max-w-[180px] truncate" title="{{ $slot1->task_name }}">{{ $slot1->task_name }}</p>
                                        </div>
                                        <span class="ml-auto shrink-0 inline-flex items-center gap-0.5 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            ⭐ {{ number_format($slot1->poin_kerja, 1) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300 italic">— Belum ada —</span>
                                @endif
                            </td>

                            {{-- Slot 2 --}}
                            <td class="px-5 py-4">
                                @if($slot2)
                                    @php $isMine2 = $slot2->assigned_to === $myId; @endphp
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full font-bold text-xs uppercase
                                            {{ $isMine2 ? 'bg-primary-600 text-white ring-2 ring-primary-300' : 'bg-indigo-100 text-indigo-700' }}">
                                            {{ substr($slot2->assignee->username, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-xs {{ $isMine2 ? 'text-primary-700' : 'text-gray-900' }}">
                                                {{ $slot2->assignee->username }}
                                                @if($isMine2) <span class="text-[9px] font-bold text-primary-500">(Anda)</span> @endif
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 max-w-[180px] truncate" title="{{ $slot2->task_name }}">{{ $slot2->task_name }}</p>
                                        </div>
                                        <span class="ml-auto shrink-0 inline-flex items-center gap-0.5 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            ⭐ {{ number_format($slot2->poin_kerja, 1) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300 italic">— Belum ada —</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DAFTAR TUGAS SAYA --}}
    {{-- ============================================================ --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Tugas Terjadwal Saya</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">Total {{ $jadwal->total() }} Tugas</span>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($jadwal as $j)
                <div class="p-6 hover:bg-gray-50/50 transition-colors flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{-- Detail Tugas --}}
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold text-primary-700 bg-primary-50 px-2.5 py-1 rounded-full whitespace-nowrap">
                                📅 Setiap Hari {{ $j->hari }}
                            </span>
                            <span class="text-[10px] text-gray-400">Diberikan oleh Admin: <strong class="capitalize">{{ $j->pembuat->username }}</strong></span>
                        </div>
                        <h4 class="text-base font-bold text-gray-950">{{ $j->task_name }}</h4>
                        @if($j->description)
                            <p class="text-sm text-gray-600 max-w-2xl leading-relaxed">
                                {{ $j->description }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 italic">Tidak ada deskripsi instruksi kerja.</p>
                        @endif
                    </div>
                    
                    {{-- Reward Poin --}}
                    <div class="shrink-0 flex items-center gap-3 sm:flex-col sm:items-end">
                        <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-3.5 py-1.5 rounded-full text-sm font-bold shadow-sm">
                            ⭐ {{ number_format($j->poin_kerja, 1) }} Poin Kerja
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                    </svg>
                    <p class="mt-4 text-sm font-medium text-gray-700">Belum ada tugas terjadwal</p>
                    <p class="mt-1 text-xs text-gray-500">Bagus! Tidak ada tugas terjadwal untuk Anda saat ini. Nikmati waktu istirahat Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($jadwal->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $jadwal->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

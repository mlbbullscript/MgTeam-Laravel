@extends('layouts.app')

@section('title', 'Penghasilan Saya')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Penghasilan & Riwayat Transfer</h1>
        <p class="mt-1 text-sm text-gray-500">Pantau riwayat penerimaan profit dan bagi hasil tim yang telah dicairkan oleh Admin.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-stat-card 
            title="Total Pendapatan Ditransfer" 
            value="Rp {{ number_format($totalDiterima, 0, ',', '.') }}" 
            subtext="Telah berhasil ditransfer ke rekening Anda" 
            type="success"
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
            subtext="Persentase porsi pool dividen profit Anda" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- History Table --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">Riwayat Pembayaran Profit</h3>
            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full">Total {{ $riwayat->total() }} Record</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Tanggal Distribusi</th>
                        <th class="px-6 py-3.5">Catatan / Deskripsi</th>
                        <th class="px-6 py-3.5 text-center">Proporsi Saat Itu</th>
                        <th class="px-6 py-3.5 text-right">Bagian Saham</th>
                        <th class="px-6 py-3.5 text-right">Bagian Kerja</th>
                        <th class="px-6 py-3.5 text-right font-extrabold text-primary-900">Total Diterima</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Tanggal --}}
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($r->distribusi->distributed_at)->translatedFormat('d M Y, H:i') }}
                            </td>

                            {{-- Catatan --}}
                            <td class="px-6 py-4">
                                @if($r->distribusi->notes)
                                    <p class="font-medium text-gray-900">{{ $r->distribusi->notes }}</p>
                                @else
                                    <p class="text-gray-400 italic text-xs">Tanpa catatan.</p>
                                @endif
                                <p class="text-[10px] text-gray-400 mt-0.5">Oleh Admin: <span class="capitalize">{{ $r->distribusi->distributor->username }}</span></p>
                            </td>

                            {{-- Proporsi --}}
                            <td class="px-6 py-4 text-center text-xs space-y-0.5">
                                <div>🪙 {{ number_format($r->coin_saham, 2) }}%</div>
                                <div class="text-amber-600 font-semibold">⭐ {{ number_format($r->poin_kerja, 1) }} Poin</div>
                            </td>

                            {{-- Bagian Saham --}}
                            <td class="px-6 py-4 text-right font-medium">
                                Rp {{ number_format($r->bagian_saham, 0, ',', '.') }}
                            </td>

                            {{-- Bagian Kerja --}}
                            <td class="px-6 py-4 text-right font-medium">
                                Rp {{ number_format($r->bagian_kerja, 0, ',', '.') }}
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 text-right font-bold text-primary-700 whitespace-nowrap bg-primary-50/20">
                                Rp {{ number_format($r->total, 0, ',', '.') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($r->status === 'ditransfer')
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Sudah Ditransfer
                                    </span>
                                @elseif($r->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-bold text-yellow-700 ring-1 ring-inset ring-yellow-600/20 animate-pulse">
                                        Menunggu Transfer
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">
                                        Gagal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-700">Belum ada riwayat distribusi</p>
                                <p class="mt-1 text-xs text-gray-500">Pembagian profit usaha yang dilakukan oleh Admin akan tercatat lengkap di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($riwayat->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

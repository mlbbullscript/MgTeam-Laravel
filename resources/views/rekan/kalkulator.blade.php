@extends('layouts.app')

@section('title', 'Kalkulator Profit Real-Time')

@section('content')
<div class="space-y-6" x-data="{
    labaSimulasi: {{ max(0, $data['laba_bersih']) }},
    pctSaham: {{ $data['pct_saham'] }},
    pctKerja: {{ $data['pct_kerja'] }},
    coinSaya: {{ $userSaya->coin_saham }},
    poinSaya: {{ $poinSaya }},
    totalPoin: {{ max(0.1, $data['total_poin']) }},
    get poolSaham() { return this.labaSimulasi * (this.pctSaham / 100); },
    get poolKerja() { return this.labaSimulasi * (this.pctKerja / 100); },
    get bagianSaham() { return (this.coinSaya / 100) * this.poolSaham; },
    get bagianKerja() { return (this.poinSaya / this.totalPoin) * this.poolKerja; },
    get total() { return this.bagianSaham + this.bagianKerja; },
    formatRupiah(val) {
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }
}">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kalkulator & Estimator Profit</h1>
        <p class="mt-1 text-sm text-gray-500">Estimasi pendapatan real-time Anda berdasarkan laba bersih kumulatif saat ini serta simulator interaktif.</p>
    </div>

    {{-- Estimasi Saat Ini & Simulator --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Estimasi Saat Ini (Left 2 Columns) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Income Estimate Card --}}
            <div class="rounded-2xl bg-gradient-to-br from-primary-800 to-primary-950 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-16 -top-16 opacity-10">
                    <svg class="h-64 w-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <p class="text-xs font-bold uppercase tracking-wider text-primary-200">Estimasi Bagian Pendapatan Anda</p>
                <p class="text-3xl sm:text-4xl font-extrabold mt-2 leading-none text-white tracking-tight" 
                   x-text="formatRupiah(total)">
                    Rp {{ number_format($bagianSaya['total'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-primary-200 mt-2 font-medium">Berdasarkan laba bersih berjalan saat ini.</p>
                
                <div class="grid grid-cols-2 gap-4 border-t border-white/20 pt-6 mt-6">
                    <div>
                        <p class="text-[10px] uppercase font-semibold text-primary-300 tracking-wider">Bagian Saham ({{ number_format($userSaya->coin_saham, 2) }}%)</p>
                        <p class="text-lg font-bold text-white mt-0.5" x-text="formatRupiah(bagianSaham)">
                            Rp {{ number_format($bagianSaya['bagian_saham'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-semibold text-primary-300 tracking-wider">Bagian Kerja ({{ number_format($poinSaya, 1) }} / {{ number_format($data['total_poin'], 1) }} ⭐)</p>
                        <p class="text-lg font-bold text-white mt-0.5" x-text="formatRupiah(bagianKerja)">
                            Rp {{ number_format($bagianSaya['bagian_kerja'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tabel Rincian Semua Rekan --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm tracking-wide">Transparansi Proporsi Rekan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-3">Rekan</th>
                                <th class="px-6 py-3 text-center">Proporsi Kontribusi</th>
                                <th class="px-6 py-3 text-right">Saham</th>
                                <th class="px-6 py-3 text-right">Poin Kerja</th>
                                <th class="px-6 py-3 text-right">Total Bagian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @foreach($data['rekan'] as $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item['user']->id === $userSaya->id ? 'bg-primary-50/30' : '' }}">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-600 text-white font-bold text-xs uppercase">
                                                {{ substr($item['user']->username, 0, 2) }}
                                            </div>
                                            <span class="font-semibold text-gray-900">
                                                {{ $item['user']->username }}
                                                @if($item['user']->id === $userSaya->id)
                                                    <span class="text-[9px] bg-primary-100 text-primary-700 font-bold px-1.5 py-0.5 rounded ml-1">Saya</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center text-xs space-y-0.5">
                                        <div>🪙 {{ number_format($item['coin_saham'], 2) }}%</div>
                                        <div class="text-amber-600 font-semibold">⭐ {{ number_format($item['poin_kerja'], 1) }} Poin</div>
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium">
                                        Rp {{ number_format($item['bagian_saham'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium">
                                        Rp {{ number_format($item['bagian_kerja'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-primary-700">
                                        Rp {{ number_format($item['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Simulator Interaktif (Right 1 Column) --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 text-sm tracking-wide flex items-center gap-2">
                    🎯 Simulator Laba Bersih
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed">Masukkan perkiraan target laba bersih usaha Anda untuk melihat estimasi pembagian dividen profit secara real-time.</p>
                
                <div class="space-y-4 border-t border-gray-100 pt-4">
                    <div>
                        <label for="laba_simulasi_input" class="block text-xs font-bold uppercase tracking-wider text-gray-500">Estimasi Laba Bersih (Rp)</label>
                        <div class="mt-1.5 relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-xs">Rp</span>
                            </div>
                            <input type="number" 
                                   id="laba_simulasi_input" 
                                   x-model.number="labaSimulasi"
                                   min="0"
                                   class="block w-full rounded-lg border-gray-300 pl-9 pr-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500" 
                                   placeholder="Masukkan angka...">
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-gray-50 pt-3">
                        <div class="flex items-center justify-between text-xs font-medium text-gray-600">
                            <span>Pool Saham (50%):</span>
                            <span class="font-bold" x-text="formatRupiah(poolSaham)"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-medium text-gray-600">
                            <span>Pool Kerja (50%):</span>
                            <span class="font-bold" x-text="formatRupiah(poolKerja)"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 p-4 rounded-lg bg-gray-50 border border-gray-100 space-y-2">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Hasil Simulasi Anda</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700 font-semibold">Saham Saya:</span>
                    <span class="font-bold text-gray-900" x-text="formatRupiah(bagianSaham)"></span>
                </div>
                <div class="flex items-center justify-between text-sm border-b border-gray-200 pb-2">
                    <span class="text-gray-700 font-semibold">Poin Saya:</span>
                    <span class="font-bold text-gray-900" x-text="formatRupiah(bagianKerja)"></span>
                </div>
                <div class="flex items-center justify-between text-base pt-1">
                    <span class="text-primary-800 font-extrabold">Total Estimasi:</span>
                    <span class="font-extrabold text-primary-800" x-text="formatRupiah(total)"></span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

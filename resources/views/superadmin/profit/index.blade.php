@extends('layouts.app')

@section('title', 'Kalkulasi & Distribusi Profit')

@section('content')
<div class="space-y-6" x-data="{ isOpen: false, actionUrl: '', modalMessage: '' }">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kalkulasi & Distribusi Profit</h1>
        <p class="mt-1 text-sm text-gray-500">Hitung alokasi profit berdasarkan proporsi koin saham (investasi) dan poin kerja (kontribusi).</p>
    </div>

    {{-- Alert Error Khusus untuk Validasi Persentase --}}
    @error('pct_saham')
        <x-alert type="error" :pesan="$message" />
    @enderror

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card 
            title="Laba Bersih Kumulatif" 
            value="Rp {{ number_format($data['laba_bersih'], 0, ',', '.') }}" 
            subtext="Semua pemasukan - pengeluaran" 
            type="{{ $data['laba_bersih'] >= 0 ? 'success' : 'danger' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Pool Saham ({{ $data['pct_saham'] }}%)" 
            value="Rp {{ number_format($data['pool_saham'], 0, ',', '.') }}" 
            subtext="Alokasi berdasarkan kepemilikan koin saham" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Pool Kerja ({{ $data['pct_kerja'] }}%)" 
            value="Rp {{ number_format($data['pool_kerja'], 0, ',', '.') }}" 
            subtext="Alokasi berdasarkan akumulasi poin kerja" 
            type="info"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Tabel Distribusi Real-Time (Left/Middle 2 Columns) --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm tracking-wide">Breakdown Profit Rekan Kerja</h3>
                    <span class="text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full">Total Poin: {{ number_format($data['total_poin'], 1) }} ⭐</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-3">Rekan</th>
                                <th class="px-6 py-3 text-center">Proporsi</th>
                                <th class="px-6 py-3 text-right">Bagian Saham</th>
                                <th class="px-6 py-3 text-right">Bagian Kerja</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($data['rekan'] as $r)
                                <tr class="hover:bg-gray-50 text-sm text-gray-700">
                                    {{-- Rekan --}}
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white font-bold text-xs uppercase">
                                                {{ substr($r['user']->username, 0, 2) }}
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $r['user']->username }}</span>
                                        </div>
                                    </td>

                                    {{-- Proporsi --}}
                                    <td class="px-6 py-3 text-center text-xs space-y-1">
                                        <div class="font-semibold text-gray-900">🪙 {{ number_format($r['coin_saham'], 2) }}%</div>
                                        <div class="text-amber-600 font-semibold">⭐ {{ number_format($r['poin_kerja'], 1) }} Poin</div>
                                    </td>

                                    {{-- Bagian Saham --}}
                                    <td class="px-6 py-3 text-right font-medium text-gray-900">
                                        Rp {{ number_format($r['bagian_saham'], 0, ',', '.') }}
                                    </td>

                                    {{-- Bagian Kerja --}}
                                    <td class="px-6 py-3 text-right font-medium text-gray-900">
                                        Rp {{ number_format($r['bagian_kerja'], 0, ',', '.') }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-3 text-right font-bold text-primary-700 whitespace-nowrap bg-primary-50/50">
                                        Rp {{ number_format($r['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada rekan aktif untuk menerima profit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Distribusi --}}
            @if(count($data['rekan']) > 0 && $data['laba_bersih'] > 0)
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <form action="{{ route('superadmin.profit.distribusi') }}" method="POST" id="form-distribusi">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="catatan" class="block text-xs font-bold uppercase tracking-wider text-gray-500">Catatan Distribusi (Opsional)</label>
                                <textarea name="catatan" 
                                          id="catatan" 
                                          rows="2" 
                                          maxlength="500"
                                          placeholder="Contoh: Distribusi Laba Bersih Bulan Mei 2026..."
                                          class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="button"
                                        id="btn-distribusikan"
                                        @click="actionUrl = '{{ route('superadmin.profit.distribusi') }}'; modalMessage = 'Apakah Anda yakin ingin mendistribusikan profit Rp {{ number_format($data['laba_bersih'], 0, ',', '.') }} saat ini? Tindakan ini akan mengunci riwayat poin kerja & porsi profit masing-masing rekan menjadi catatan penagihan transfer.'; isOpen = true;"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-5 py-3 shadow-md transition-colors w-full sm:w-auto">
                                    🚀 Lakukan Distribusi Profit Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-6 bg-yellow-50 border-t border-yellow-100 text-yellow-800 text-sm flex items-start gap-2.5">
                    <svg class="h-5 w-5 text-yellow-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold">Distribusi Ditangguhkan</p>
                        <p class="mt-0.5 text-xs text-yellow-700">Distribusi tidak dapat dilakukan jika Laba Bersih kurang dari atau sama dengan Rp 0, atau jika tidak ada rekan terdaftar.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Pengaturan Global (Right Column) --}}
        <div class="space-y-6">
            {{-- Pengaturan Alokasi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm tracking-wide mb-4 flex items-center gap-2">
                    ⚙️ Atur Alokasi Profit
                </h3>
                <form action="{{ route('superadmin.settings.alokasi') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label for="pct_saham" class="block text-xs font-semibold text-gray-700">Porsi Pool Saham (%)</label>
                            <input type="number" 
                                   name="pct_saham" 
                                   id="pct_saham" 
                                   value="{{ $setting['pct_saham'] }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   required
                                   class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label for="pct_kerja" class="block text-xs font-semibold text-gray-700">Porsi Pool Kerja (%)</label>
                            <input type="number" 
                                   name="pct_kerja" 
                                   id="pct_kerja" 
                                   value="{{ $setting['pct_kerja'] }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   required
                                   class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2 text-sm border focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="text-[11px] text-gray-400">
                            * Total persentase (Saham + Kerja) <strong>wajib berjumlah tepat 100%</strong>.
                        </div>
                        <button type="submit" 
                                id="btn-update-alokasi"
                                class="w-full inline-flex items-center justify-center rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-semibold text-sm px-4 py-2 shadow-sm transition-colors">
                            Simpan Porsi Alokasi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hak Akses Rekan (Upload Laporan) --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm tracking-wide mb-4 flex items-center gap-2">
                    🔒 Hak Akses Rekan
                </h3>
                @php
                    $izinUpload = \App\Models\Setting::izinUploadRekan();
                @endphp
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3.5 rounded-lg border {{ $izinUpload ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                        <div>
                            <p class="text-xs font-bold text-gray-900">Upload Laporan Keuangan</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Apakah rekan boleh input pemasukan/pengeluaran?</p>
                        </div>
                        <span class="text-xs font-bold uppercase {{ $izinUpload ? 'text-green-700' : 'text-red-700' }}">
                            {{ $izinUpload ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <form action="{{ route('superadmin.settings.izin-upload') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                id="btn-toggle-izin-upload"
                                class="w-full inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors {{ $izinUpload ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                            {{ $izinUpload ? 'Matikan Izin Upload Rekan' : 'Aktifkan Izin Upload Rekan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Reusable Confirmation Modal --}}
    <x-modal-confirm 
        id="form-distribusi-modal"
        method="POST"
        title="Distribusi Profit Induk"
        confirmText="Ya, Distribusikan!"
        type="success"
    />
</div>
@endsection

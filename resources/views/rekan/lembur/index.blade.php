@extends('layouts.app')

@section('title', 'Tugas Lembur Rekan')

@section('content')
<div class="space-y-6" x-data="{ isOpen: false, actionUrl: '', modalMessage: '' }">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ambil Tugas Lembur</h1>
        <p class="mt-1 text-sm text-gray-500">Dapatkan poin kerja tambahan dengan mengklaim tugas lembur opsional yang tersedia (siapa cepat dia dapat).</p>
    </div>

    {{-- Two columns layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Overtime Tersedia (Left Column) --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-emerald-900 text-sm tracking-wide flex items-center gap-1.5">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Tugas Lembur Tersedia
                    </h3>
                    <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full">{{ $tersedia->count() }} Tersedia</span>
                </div>
                <div class="divide-y divide-gray-200 max-h-[500px] overflow-y-auto">
                    @forelse($tersedia as $t)
                        <div class="p-6 hover:bg-gray-50/50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1">
                                <h4 class="font-bold text-gray-950">{{ $t->name }}</h4>
                                @if($t->description)
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $t->description }}</p>
                                @else
                                    <p class="text-xs text-gray-400 italic">Tidak ada deskripsi detail.</p>
                                @endif
                                <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-[10px] font-bold">
                                    ⭐ {{ number_format($t->poin_kerja, 1) }} Poin Kerja
                                </div>
                            </div>
                            <div class="shrink-0">
                                <button 
                                    @click="actionUrl = '{{ route('rekan.lembur.ambil', $t->id) }}'; modalMessage = 'Apakah Anda yakin ingin mengambil tugas lembur {{ $t->name }}? Rekan lain tidak akan bisa mengambilnya lagi.'; isOpen = true;"
                                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 py-2 shadow-sm transition-colors"
                                    id="btn-ambil-lembur-{{ $t->id }}">
                                    Klaim Tugas
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                            </svg>
                            <p class="mt-4 text-xs font-semibold text-gray-700">Tidak ada lembur tersedia</p>
                            <p class="mt-0.5 text-[11px] text-gray-500">Admin belum menambahkan tugas lembur baru saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Overtime Diambilku (Right Column) --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm tracking-wide">
                        Lembur Saya Bulan Ini
                    </h3>
                    <span class="text-xs font-bold bg-primary-100 text-primary-800 px-2.5 py-1 rounded-full">{{ $diambilku->count() }} Diambil</span>
                </div>
                <div class="divide-y divide-gray-200 max-h-[500px] overflow-y-auto">
                    @forelse($diambilku as $d)
                        <div class="p-6 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <h4 class="font-bold text-gray-950">{{ $d->name }}</h4>
                                    @if($d->description)
                                        <p class="text-xs text-gray-600 leading-relaxed">{{ $d->description }}</p>
                                    @endif
                                    <div class="text-[10px] text-gray-400">
                                        Diambil pada: <strong>{{ \Carbon\Carbon::parse($d->taken_at)->translatedFormat('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        ⭐ {{ number_format($d->poin_kerja, 1) }}
                                    </div>
                                    <span class="block text-[10px] text-green-600 font-bold mt-1">Berhasil Klaim</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 text-xs font-semibold text-gray-700">Belum ada lembur diambil</p>
                            <p class="mt-0.5 text-[11px] text-gray-500">Ambil tugas lembur di kolom kiri untuk meningkatkan akumulasi poin kerja Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Reusable Confirmation Modal --}}
    <x-modal-confirm 
        id="form-ambil-lembur-modal"
        method="POST"
        title="Ambil Tugas Lembur"
        confirmText="Ya, Ambil Tugas"
        type="success"
    />
</div>
@endsection

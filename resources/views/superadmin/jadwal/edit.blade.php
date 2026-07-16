@extends('layouts.app')

@section('title', 'Edit Jadwal Kerja')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.jadwal.index') }}" 
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 shadow-sm transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Jadwal Kerja</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data penugasan atau alokasi poin kerja. <strong>Maks. 2 orang per hari.</strong></p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('superadmin.jadwal.update', $jadwal->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Hari Penugasan --}}
                <div>
                    <label for="hari" class="block text-sm font-semibold text-gray-700">Hari Penugasan (Berulang)</label>
                    <div class="mt-1">
                        <select name="hari" 
                                id="hari" 
                                class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('hari') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                required>
                            <option value="">-- Pilih Hari --</option>
                            @php
                                $daftarHariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            @endphp
                            @foreach($daftarHariList as $h)
                                @php $isPenuh = in_array($h, $slotPenuh ?? []); @endphp
                                <option value="{{ $h }}"
                                    {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}
                                    {{ $isPenuh ? 'disabled' : '' }}>
                                    Setiap Hari {{ $h }}{{ $isPenuh ? ' (Penuh)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('hari')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ditugaskan Ke --}}
                <div>
                    <label for="assigned_to" class="block text-sm font-semibold text-gray-700">Ditugaskan Kepada</label>
                    <div class="mt-1">
                        <select name="assigned_to" 
                                id="assigned_to" 
                                class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('assigned_to') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                required>
                            <option value="">-- Pilih Rekan --</option>
                            @foreach($rekan as $r)
                                <option value="{{ $r->id }}" {{ old('assigned_to', $jadwal->assigned_to) == $r->id ? 'selected' : '' }}>
                                    {{ $r->username }} (Saham: {{ number_format($r->coin_saham, 2) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('assigned_to')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Tugas --}}
                <div class="sm:col-span-2">
                    <label for="task_name" class="block text-sm font-semibold text-gray-700">Nama Tugas</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="task_name" 
                               id="task_name" 
                               value="{{ old('task_name', $jadwal->task_name) }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('task_name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Contoh: CS Pagi, Update Stock Opname, Posting Sosmed"
                               required>
                    </div>
                    @error('task_name')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Detail Tugas (Opsional)</label>
                    <div class="mt-1">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                  placeholder="Tulis instruksi pengerjaan tugas di sini secara jelas...">{{ old('description', $jadwal->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info slot penuh --}}
                @if(!empty($slotPenuh))
                <div class="sm:col-span-2">
                    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-3">
                        <svg class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        <p class="text-xs text-amber-800">
                            Hari <strong>{{ implode(', ', $slotPenuh) }}</strong> sudah penuh (2 orang). Pilih hari yang masih tersedia.
                        </p>
                    </div>
                </div>
                @endif

                {{-- Poin Kerja --}}
                <div>
                    <label for="poin_kerja" class="block text-sm font-semibold text-gray-700">Poin Kerja Diberikan</label>
                    <div class="mt-1">
                        <input type="number" 
                               name="poin_kerja" 
                               id="poin_kerja" 
                               value="{{ old('poin_kerja', $jadwal->poin_kerja) }}"
                               step="0.1"
                               min="0"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('poin_kerja') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               required>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Poin ini akan diakumulasikan ke poin kerja rekan.</p>
                    @error('poin_kerja')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.jadwal.index') }}" 
                   class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        id="submit-jadwal"
                        class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Lembur Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.lembur.index') }}" 
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 shadow-sm transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Tugas Lembur Baru</h1>
            <p class="mt-1 text-sm text-gray-500">Buat tugas lembur tambahan yang dapat diambil secara sukarela oleh rekan kerja.</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('superadmin.lembur.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-6">
                {{-- Nama Lembur --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Tugas Lembur</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Contoh: Packing Tambahan Pesanan Shopee, CS Malam"
                               required>
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Detail Lembur (Opsional)</label>
                    <div class="mt-1">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                  placeholder="Tulis detail pekerjaan lembur yang harus diselesaikan rekan..."></textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Poin Lembur --}}
                <div class="w-full sm:w-1/2">
                    <label for="poin_kerja" class="block text-sm font-semibold text-gray-700">Poin Lembur Diberikan</label>
                    <div class="mt-1">
                        <input type="number" 
                               name="poin_kerja" 
                               id="poin_kerja" 
                               value="{{ old('poin_kerja', '1.0') }}"
                               step="0.1"
                               min="0"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('poin_kerja') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               required>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Poin kerja yang didapatkan rekan setelah berhasil mengambil tugas lembur ini.</p>
                    @error('poin_kerja')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.lembur.index') }}" 
                   class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        id="submit-lembur"
                        class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    Simpan Lembur
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

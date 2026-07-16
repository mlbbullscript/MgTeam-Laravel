@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.laporan.index') }}" 
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 shadow-sm transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Catatan Transaksi</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui detail nominal atau bukti transaksi operasional.</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('superadmin.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Tipe Transaksi --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700">Tipe Transaksi</label>
                    <div class="mt-1">
                        <select name="type" 
                                id="type" 
                                class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('type') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                required>
                            <option value="pemasukan" {{ old('type', $laporan->type) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('type', $laporan->type) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Transaksi --}}
                <div>
                    <label for="report_date" class="block text-sm font-semibold text-gray-700">Tanggal Transaksi</label>
                    <div class="mt-1">
                        <input type="date" 
                               name="report_date" 
                               id="report_date" 
                               value="{{ old('report_date', $laporan->report_date) }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('report_date') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               required>
                    </div>
                    @error('report_date')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Transaksi --}}
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Transaksi / Judul</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $laporan->name) }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Contoh: Pembayaran Invoice #123, Beli Kertas Print"
                               required>
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nominal Rupiah --}}
                <div class="sm:col-span-2">
                    <label for="amount" class="block text-sm font-semibold text-gray-700">Nominal Rupiah (Rp)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               value="{{ old('amount', $laporan->amount) }}"
                               min="1"
                               class="block w-full rounded-lg border-gray-300 pl-10 pr-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('amount') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="0"
                               required>
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi Tambahan (Opsional)</label>
                    <div class="mt-1">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                  placeholder="Catatan kecil pendukung transaksi kas...">{{ old('description', $laporan->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lampiran Screenshot Bukti --}}
                <div class="sm:col-span-2" x-data="{ fileName: null, filePreview: '{{ $laporan->screenshot ? $laporan->screenshot_url : '' }}' }">
                    <label class="block text-sm font-semibold text-gray-700">Bukti Screenshot / Nota (Opsional)</label>
                    <div class="mt-2 flex items-center gap-5">
                        <div class="h-16 w-16 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shrink-0 flex items-center justify-center">
                            <template x-if="filePreview">
                                <img :src="filePreview" class="h-16 w-16 object-cover rounded-lg">
                            </template>
                            <template x-if="!filePreview">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <input type="file" 
                                   name="screenshot" 
                                   id="screenshot" 
                                   class="hidden" 
                                   accept="image/*,application/pdf"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           fileName = file.name;
                                           if (file.type.startsWith('image/')) {
                                               const reader = new FileReader();
                                               reader.onload = (e) => { filePreview = e.target.result; };
                                               reader.readAsDataURL(file);
                                           } else {
                                               filePreview = null;
                                           }
                                       }
                                   ">
                            <button type="button" 
                                    @click="$refs.fileInput.click()"
                                    class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                                    onclick="document.getElementById('screenshot').click()">
                                Unggah File Bukti Baru
                            </button>
                            <span class="text-xs text-gray-500 ml-2" x-text="fileName || 'Maksimal 2MB (JPG/PNG/PDF)'"></span>
                        </div>
                    </div>
                    @error('screenshot')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.laporan.index') }}" 
                   class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        id="submit-laporan"
                        class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

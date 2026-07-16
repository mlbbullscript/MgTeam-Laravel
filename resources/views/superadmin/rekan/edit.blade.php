@extends('layouts.app')

@section('title', 'Edit Rekan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.rekan.index') }}" 
           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 shadow-sm transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Rekan: {{ $rekan->username }}</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data profil, kepemilikan koin saham, atau status keaktifan rekan.</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('superadmin.rekan.update', $rekan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Username --}}
                <div class="sm:col-span-2">
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" 
                               name="username" 
                               id="username" 
                               value="{{ old('username', $rekan->username) }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('username') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Contoh: budisusanto"
                               required>
                    </div>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="sm:col-span-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                    <div class="mt-1">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Isi hanya jika ingin mengganti password">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah password rekan.</p>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Koin Saham --}}
                <div>
                    <label for="coin_saham" class="block text-sm font-semibold text-gray-700">Alokasi Koin Saham (%)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" 
                               name="coin_saham" 
                               id="coin_saham" 
                               value="{{ old('coin_saham', $rekan->coin_saham) }}"
                               step="0.01"
                               min="0"
                               max="100"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('coin_saham') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               required>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Sisa koin tersedia (termasuk milik rekan ini): <strong>{{ number_format($sisaKoin, 2) }} koin</strong>.</p>
                    @error('coin_saham')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex flex-col justify-end">
                    <div class="flex items-center h-11">
                        <input id="is_active" 
                               name="is_active" 
                               type="checkbox" 
                               value="1"
                               {{ old('is_active', $rekan->is_active) ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        <label for="is_active" class="ml-3 text-sm font-semibold text-gray-700 cursor-pointer select-none">
                            Akun Aktif (Dapat Login)
                        </label>
                    </div>
                </div>

                {{-- Foto Profil --}}
                <div class="sm:col-span-2" x-data="{ photoName: null, photoPreview: '{{ $rekan->photo_profile ? $rekan->photo_profile_url : '' }}' }">
                    <label class="block text-sm font-semibold text-gray-700">Foto Profil</label>
                    <div class="mt-2 flex items-center gap-5">
                        <!-- Preview container -->
                        <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shrink-0 flex items-center justify-center">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="h-16 w-16 object-cover rounded-full">
                            </template>
                            <template x-if="!photoPreview">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </div>
                        
                        <!-- File Input -->
                        <div>
                            <input type="file" 
                                   name="photo_profile" 
                                   id="photo_profile" 
                                   class="hidden" 
                                   accept="image/*"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           photoName = file.name;
                                           const reader = new FileReader();
                                           reader.onload = (e) => { photoPreview = e.target.result; };
                                           reader.readAsDataURL(file);
                                       }
                                   ">
                            <button type="button" 
                                    @click="$refs.fileInput.click()"
                                    class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                                    x-ref="fileInputBtn"
                                    onclick="document.getElementById('photo_profile').click()">
                                Pilih Gambar Baru
                            </button>
                            <span class="text-xs text-gray-500 ml-2" x-text="photoName || 'Maksimal 2MB (JPG/PNG)'"></span>
                        </div>
                    </div>
                    @error('photo_profile')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.rekan.index') }}" 
                   class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        id="submit-rekan"
                        class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

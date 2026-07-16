@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pengaturan Profil</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola informasi pribadi, foto profil, dan kata sandi akun Anda.</p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('rekan.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Foto Profil Section --}}
            <div class="border-b border-gray-100 pb-6" x-data="{ photoName: null, photoPreview: '{{ $user->photo_profile ? $user->photo_profile_url : '' }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Profil Anda</label>
                <div class="flex items-center gap-6">
                    <!-- Preview Container -->
                    <div class="h-20 w-20 rounded-full overflow-hidden bg-primary-600 border border-gray-200 shrink-0 flex items-center justify-center shadow-md">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="h-20 w-20 object-cover rounded-full">
                        </template>
                        <template x-if="!photoPreview">
                            <span class="text-2xl font-bold text-white uppercase">
                                {{ substr($user->username, 0, 2) }}
                            </span>
                        </template>
                    </div>

                    <!-- Upload Input -->
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
                                @click="document.getElementById('photo_profile').click()"
                                class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                            Pilih Foto Baru
                        </button>
                        <p class="text-[10px] text-gray-500 mt-1.5" x-text="photoName || 'Format JPG atau PNG (Maksimal 2MB)'"></p>
                    </div>
                </div>
                @error('photo_profile')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Username --}}
                <div class="sm:col-span-2">
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="username" 
                               id="username" 
                               value="{{ old('username', $user->username) }}"
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('username') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               required>
                    </div>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                    <div class="mt-1">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Kosongkan jika tidak diganti">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                    <div class="mt-1">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="block w-full rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 border focus:border-primary-500 focus:ring-primary-500"
                               placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                <button type="submit" 
                        id="submit-profil"
                        class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors w-full sm:w-auto">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

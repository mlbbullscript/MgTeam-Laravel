@extends('layouts.auth')

@section('title', 'Masuk ke Sistem')

@section('content')

    {{-- ===== JUDUL ===== --}}
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-white">Selamat Datang</h2>
        <p class="mt-1 text-sm text-gray-400">Silahkan Login Brader</p>
    </div>

    {{-- ===== PESAN ERROR GLOBAL ===== --}}
    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-500/20 border border-red-500/30 px-4 py-3">
            <p class="text-sm text-red-300">
                {{ $errors->first() }}
            </p>
        </div>
    @endif

    {{-- ===== FORM LOGIN ===== --}}
    <form action="{{ route('login.proses') }}" method="POST" id="form-login" class="space-y-5">
        @csrf

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-medium text-gray-300 mb-1.5">
                Username
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    autocomplete="username" placeholder="Masukkan username Anda" class="block w-full rounded-lg border pl-10 pr-4 py-2.5 text-sm
                               bg-white/10 border-white/20 text-white placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                               transition-colors
                               @error('username') border-red-500 @enderror">
            </div>
            @error('username')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ tampilPassword: false }">
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">
                Password
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input :type="tampilPassword ? 'text' : 'password'" id="password" name="password" required
                    autocomplete="current-password" placeholder="Masukkan password Anda" class="block w-full rounded-lg border pl-10 pr-11 py-2.5 text-sm
                               bg-white/10 border-white/20 text-white placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                               transition-colors
                               @error('password') border-red-500 @enderror">
                {{-- Toggle tampil/sembunyikan password --}}
                <button type="button" @click="tampilPassword = !tampilPassword" id="btn-toggle-password"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-300 transition-colors"
                    :aria-label="tampilPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                    <svg x-show="!tampilPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="tampilPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ingat Saya --}}
        <div class="flex items-center">
            <input type="checkbox" id="ingat_saya" name="ingat_saya" class="h-4 w-4 rounded border-white/30 bg-white/10 text-primary-600
                           focus:ring-primary-500 focus:ring-offset-0 cursor-pointer">
            <label for="ingat_saya" class="ml-2 block text-sm text-gray-400 cursor-pointer">
                Ingat saya selama 30 hari
            </label>
        </div>

        {{-- Tombol Login --}}
        <button type="submit" id="btn-login" class="w-full flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5
                       text-sm font-semibold text-white shadow-lg
                       hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-transparent
                       active:scale-[0.98] transition-all duration-150">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Masuk ke Sistem
        </button>
    </form>

    {{-- ===== FOOTER HALAMAN LOGIN ===== --}}
    <p class="mt-8 text-center text-xs text-gray-600">
        &copy; {{ date('Y') }} Bisnis Tytyd &mdash; Manage Team
    </p>

@endsection
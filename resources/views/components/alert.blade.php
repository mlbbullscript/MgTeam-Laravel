{{--
    Komponen Alert — Tampilkan pesan notifikasi (success, error, warning, info)

    Penggunaan:
      <x-alert type="success" pesan="Data berhasil disimpan." />
      <x-alert type="error" pesan="Terjadi kesalahan." />

    @prop string $type  -- 'success' | 'error' | 'warning' | 'info'
    @prop string $pesan -- teks notifikasi yang ditampilkan
--}}

@props([
    'type'  => 'info',
    'pesan' => '',
])

@php
    $konfigurasi = [
        'success' => [
            'bg'    => 'bg-green-50 border-green-200',
            'text'  => 'text-green-800',
            'icon'  => 'text-green-500',
            'svg'   => 'M5 13l4 4L19 7',
        ],
        'error' => [
            'bg'    => 'bg-red-50 border-red-200',
            'text'  => 'text-red-800',
            'icon'  => 'text-red-500',
            'svg'   => 'M6 18L18 6M6 6l12 12',
        ],
        'warning' => [
            'bg'    => 'bg-yellow-50 border-yellow-200',
            'text'  => 'text-yellow-800',
            'icon'  => 'text-yellow-500',
            'svg'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'info' => [
            'bg'    => 'bg-blue-50 border-blue-200',
            'text'  => 'text-blue-800',
            'icon'  => 'text-blue-500',
            'svg'   => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $style = $konfigurasi[$type] ?? $konfigurasi['info'];
@endphp

<div
    x-data="{ tampil: true }"
    x-show="tampil"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="flex items-start gap-3 rounded-lg border px-4 py-3 {{ $style['bg'] }}"
    role="alert"
>
    {{-- Ikon --}}
    <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $style['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['svg'] }}"/>
    </svg>

    {{-- Pesan --}}
    <p class="flex-1 text-sm {{ $style['text'] }}">{{ $pesan }}</p>

    {{-- Tombol Tutup --}}
    <button
        @click="tampil = false"
        class="{{ $style['icon'] }} hover:opacity-70 transition-opacity"
        aria-label="Tutup notifikasi"
    >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

@props([
    'id',
    'action' => '',
    'method' => 'POST',
    'title' => 'Konfirmasi Aksi',
    'message' => 'Apakah Anda yakin ingin melakukan aksi ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'type' => 'danger', // danger, primary, success, warning
])

@php
    $colors = [
        'danger' => [
            'iconBg' => 'bg-rose-50 text-rose-600',
            'btn' => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500',
            'svg' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'primary' => [
            'iconBg' => 'bg-blue-50 text-blue-600',
            'btn' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'svg' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'success' => [
            'iconBg' => 'bg-emerald-50 text-emerald-600',
            'btn' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500',
            'svg' => 'M5 13l4 4L19 7',
        ],
        'warning' => [
            'iconBg' => 'bg-amber-50 text-amber-600',
            'btn' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
            'svg' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
    ];

    $color = $colors[$type] ?? $colors['danger'];
@endphp

<div
    x-show="isOpen"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isOpen = false"></div>

        {{-- Panel Konten --}}
        <div
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <form id="{{ $id }}" :action="actionUrl" method="POST">
                @csrf
                @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                    @method($method)
                @endif

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 {{ $color['iconBg'] }}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $color['svg'] }}"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">{{ $title }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="modalMessage || '{{ $message }}'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button
                        type="submit"
                        class="inline-flex w-full justify-center rounded-lg px-3 py-2 text-sm font-semibold text-white shadow-sm sm:w-auto transition-colors {{ $color['btn'] }}"
                    >
                        {{ $confirmText }}
                    </button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors"
                        @click="isOpen = false"
                    >
                        {{ $cancelText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

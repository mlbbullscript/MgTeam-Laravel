<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bisnis Tytyd') — Hanya Untuk Kaum Tertentu</title>
    <meta name="description"
        content="@yield('meta-description', 'Sistem manajemen tim yang adil, jelas, dan transparan.')">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>

<body class="h-full font-sans" x-data="{ sidebarTerbuka: false }">

    {{-- =========================================================
    LAYOUT UTAMA — Sidebar + Konten
    ========================================================= --}}
    <div class="flex h-full">

        {{-- ===== OVERLAY MOBILE ===== --}}
        <div class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-show="sidebarTerbuka"
            x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarTerbuka = false">
        </div>

        {{-- ===== SIDEBAR ===== --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-gray-900 shadow-xl
               transform transition-transform duration-200 ease-in-out
               lg:relative lg:translate-x-0"
            :class="sidebarTerbuka ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            {{-- Logo --}}
            <div class="flex h-16 shrink-0 items-center gap-2 px-6 border-b border-gray-700">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white leading-tight">BisnisTim</p>
                    <p class="text-xs text-gray-400 leading-tight">Manajemen</p>
                </div>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                @include('components.sidebar')
            </nav>

            {{-- Info User --}}
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600">
                        @if(auth()->user()->photo_profile)
                            <img src="{{ auth()->user()->photo_profile_url }}" alt="Foto profil"
                                class="h-9 w-9 rounded-full object-cover">
                        @else
                            <span class="text-sm font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->username }}</p>
                        <p class="truncate text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" id="btn-logout" class="text-gray-400 hover:text-white transition-colors"
                            title="Keluar">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ===== KONTEN UTAMA ===== --}}
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            {{-- Header Mobile --}}
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b bg-white px-4 shadow-sm lg:hidden">
                <button id="btn-buka-sidebar" @click="sidebarTerbuka = !sidebarTerbuka"
                    class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-sm font-semibold text-gray-900">Manajemen Team</span>
            </header>

            {{-- Flash Messages --}}
            <div class="px-4 pt-4 lg:px-6 space-y-2">
                @if(session('success'))
                    <x-alert type="success" :pesan="session('success')" />
                @endif
                @if(session('error'))
                    <x-alert type="error" :pesan="session('error')" />
                @endif
                @if(session('warning'))
                    <x-alert type="warning" :pesan="session('warning')" />
                @endif
            </div>

            {{-- Konten Halaman --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
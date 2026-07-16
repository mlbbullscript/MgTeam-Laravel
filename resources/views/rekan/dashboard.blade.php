@extends('layouts.app')

@section('title', 'Dashboard Rekan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Selamat datang, <strong>{{ auth()->user()->username }}</strong>! Berikut adalah ikhtisar kontribusi dan estimasi bagi hasil Anda.</p>
    </div>

    {{-- Metrik Cards Personal --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Poin Kerja Personal --}}
        <x-stat-card 
            title="Poin Kerja Saya" 
            value="{{ number_format($poinSaya, 1) }} Poin" 
            subtext="Akumulasi tugas & lembur bulan ini" 
            type="warning"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Koin Saham Personal --}}
        <x-stat-card 
            title="Koin Saham Anda" 
            value="{{ number_format($coinSaham, 2) }} %" 
            subtext="Porsi modal kepemilikan Anda" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Estimasi Pendapatan Dividen --}}
        <x-stat-card 
            title="Estimasi Pendapatan" 
            value="Rp {{ number_format($estimasiProfit, 0, ',', '.') }}" 
            subtext="Bagian dividen berjalan Anda" 
            type="success"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

    </div>

    {{-- Grafik Aktivitas Poin Personal --}}
    <div class="grid grid-cols-1 gap-6">
        <div>
            <x-chart-card title="Grafik Perolehan Poin Kerja Saya (7 Hari Terakhir)" chartId="chartPoin" height="h-80" />
        </div>
    </div>

    {{-- =====================================================================
         TRANSPARANSI GLOBAL — METRIK & CHART FINANSIAL / SAHAM TIM
         ===================================================================== --}}
    <div class="border-t border-gray-200 pt-6 mt-8">
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Transparansi Finansial & Kepemilikan Tim</h2>
        <p class="mt-1 text-sm text-gray-500">Seluruh data pergerakan kas keuangan dan porsi saham disajikan terbuka agar tercipta keadilan dan kepercayaan penuh.</p>
    </div>

    {{-- Metrik Keuangan Global --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        {{-- Laba Bersih --}}
        <x-stat-card 
            title="Laba Bersih Kumulatif" 
            value="Rp {{ number_format($labaBersih, 0, ',', '.') }}" 
            subtext="Laba kas bersih operasional tim" 
            type="{{ $labaBersih >= 0 ? 'success' : 'danger' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pemasukan --}}
        <x-stat-card 
            title="Total Pemasukan Tim" 
            value="Rp {{ number_format($totalPemasukan, 0, ',', '.') }}" 
            subtext="Seluruh nominal transaksi kas masuk" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pengeluaran --}}
        <x-stat-card 
            title="Total Pengeluaran Tim" 
            value="Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}" 
            subtext="Seluruh nominal transaksi kas keluar" 
            type="danger"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0-5a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

    </div>

    {{-- Grafik Keuangan & Saham Global --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Line Chart Arus Kas (Left) --}}
        <div class="lg:col-span-2">
            <x-chart-card title="Arus Keuangan Tim (30 Hari Terakhir)" chartId="chartKeuangan" height="h-80" />
        </div>

        {{-- Pie Chart Saham (Right) --}}
        <div>
            <x-chart-card title="Peta Distribusi Koin Saham Tim" chartId="chartSaham" height="h-80" />
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Line Chart Perolehan Poin Kerja Saya ---
        const ctxPoin = document.getElementById('chartPoin').getContext('2d');
        new Chart(ctxPoin, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Poin Kerja Harian',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#d97706', // Amber-600
                    backgroundColor: 'rgba(217, 119, 6, 0.05)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#d97706',
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Inter', size: 12 },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(1) + ' Poin';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { family: 'Inter', size: 11 }
                        },
                        suggestedMax: 5
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });

        // --- 2. Line Chart Arus Keuangan Global ---
        const ctxKeuangan = document.getElementById('chartKeuangan').getContext('2d');
        new Chart(ctxKeuangan, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels30) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($pemasukanChartData) !!},
                        borderColor: '#2563eb', // Blue-600
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#2563eb',
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($pengeluaranChartData) !!},
                        borderColor: '#f43f5e', // Rose-500
                        backgroundColor: 'rgba(244, 63, 94, 0.05)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#f43f5e',
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Inter', size: 12 },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                return 'Rp ' + (value >= 1000000 ? (value / 1000000) + 'M' : (value >= 1000 ? (value / 1000) + 'Rb' : value));
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });

        // --- 3. Pie Chart Saham Rekan ---
        const ctxSaham = document.getElementById('chartSaham').getContext('2d');
        new Chart(ctxSaham, {
            type: 'pie',
            data: {
                labels: {!! json_encode($sahamLabels) !!},
                datasets: [{
                    data: {!! json_encode($sahamValues) !!},
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#8b5cf6', // violet
                        '#ec4899', // pink
                        '#06b6d4', // cyan
                        '#f43f5e', // rose
                        '#9ca3af'  // gray (for "Sisa Tersedia")
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Inter', size: 11 },
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const val = context.raw || 0;
                                return ` ${label}: ${val.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

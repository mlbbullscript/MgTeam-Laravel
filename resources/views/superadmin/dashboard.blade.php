@extends('layouts.app')

@section('title', 'Dashboard SuperAdmin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Ringkasan Bisnis</h1>
        <p class="mt-1 text-sm text-gray-500">Selamat datang kembali, <strong>{{ auth()->user()->username }}</strong>! Berikut adalah ikhtisar operasional bisnis saat ini.</p>
    </div>

    {{-- Metrik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card 
            title="Laba Bersih Kumulatif" 
            value="Rp {{ number_format($labaBersih, 0, ',', '.') }}" 
            subtext="Laba setelah dikurangi pengeluaran" 
            type="{{ $labaBersih >= 0 ? 'success' : 'danger' }}"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Total Pemasukan" 
            value="Rp {{ number_format($totalPemasukan, 0, ',', '.') }}" 
            subtext="Semua transaksi kas masuk" 
            type="primary"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Total Pengeluaran" 
            value="Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}" 
            subtext="Semua transaksi kas keluar" 
            type="danger"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0-5a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Rekan Kerja Aktif" 
            value="{{ $jumlahRekanAktif }} Orang" 
            subtext="Rekan penggerak bisnis saat ini" 
            type="info"
        >
            <x-slot:icon>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Grafik / Visualisasi data --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Line Chart Keuangan (Left Column) --}}
        <div class="lg:col-span-2">
            <x-chart-card title="Arus Keuangan (30 Hari Terakhir)" chartId="chartKeuangan" height="h-80" />
        </div>

        {{-- Pie Chart Saham (Right Column) --}}
        <div>
            <x-chart-card title="Distribusi Kepemilikan Koin Saham" chartId="chartSaham" height="h-80" />
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Line Chart Arus Keuangan ---
        const ctxKeuangan = document.getElementById('chartKeuangan').getContext('2d');
        new Chart(ctxKeuangan, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
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

        // --- 2. Pie Chart Saham Rekan ---
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

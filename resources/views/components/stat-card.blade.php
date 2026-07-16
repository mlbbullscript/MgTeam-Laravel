@props([
    'title' => '',
    'value' => '',
    'subtext' => '',
    'type' => 'primary', // primary, success, danger, warning, info
])

@php
    $styles = [
        'primary' => [
            'bg' => 'bg-blue-50 border-blue-100',
            'text' => 'text-blue-900',
            'sub' => 'text-blue-600',
            'iconBg' => 'bg-blue-600 text-white',
        ],
        'success' => [
            'bg' => 'bg-emerald-50 border-emerald-100',
            'text' => 'text-emerald-900',
            'sub' => 'text-emerald-600',
            'iconBg' => 'bg-emerald-600 text-white',
        ],
        'danger' => [
            'bg' => 'bg-rose-50 border-rose-100',
            'text' => 'text-rose-900',
            'sub' => 'text-rose-600',
            'iconBg' => 'bg-rose-600 text-white',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 border-amber-100',
            'text' => 'text-amber-900',
            'sub' => 'text-amber-600',
            'iconBg' => 'bg-amber-600 text-white',
        ],
        'info' => [
            'bg' => 'bg-sky-50 border-sky-100',
            'text' => 'text-sky-900',
            'sub' => 'text-sky-600',
            'iconBg' => 'bg-sky-600 text-white',
        ],
    ];

    $style = $styles[$type] ?? $styles['primary'];
@endphp

<div class="rounded-xl border p-6 {{ $style['bg'] }} shadow-sm flex items-center gap-4 transition-transform hover:scale-[1.02] duration-200">
    @if(isset($icon))
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $style['iconBg'] }}">
            {{ $icon }}
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $title }}</p>
        <p class="text-2xl font-bold mt-1 {{ $style['text'] }} truncate">{{ $value }}</p>
        @if($subtext)
            <p class="text-xs mt-0.5 {{ $style['sub'] }} truncate">{{ $subtext }}</p>
        @endif
    </div>
</div>

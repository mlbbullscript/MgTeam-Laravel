@props([
    'title' => '',
    'chartId' => '',
    'height' => 'h-72',
])

<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
    @if($title)
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800 text-sm tracking-wide">{{ $title }}</h3>
            @if(isset($action))
                <div>
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    <div class="relative w-full {{ $height }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>

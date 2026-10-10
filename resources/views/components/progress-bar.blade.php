<div {{ $attributes->merge(['class' => 'w-full']) }}>
    {{-- Label Row --}}
    @if ($label || $showLabel)
        <div class="flex items-center justify-between mb-1.5">
            @if ($label)
                <span class="text-[12px] font-medium leading-[1.5] text-[#1a2233]">{{ $label }}</span>
            @endif
            @if ($showLabel)
                <span class="text-[12px] leading-[1.5] text-[#66758c]">{{ number_format($clamped, 0) }}%</span>
            @endif
        </div>
    @endif

    {{-- Bar Track --}}
    <div class="w-full rounded-full bg-[#e9edf3] {{ $sizeClass }} overflow-hidden">
        <div
            class="h-full rounded-full transition-all duration-500 ease-out {{ $barColorClass }}"
            style="width: {{ $clamped }}%"
            role="progressbar"
            aria-valuenow="{{ $clamped }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
</div>

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 p-5 rounded-[12px] bg-white border border-[#dfe6ef]']) }}>
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <p class="text-[13px] leading-[1.45] text-[#66758c]">{{ $label }}</p>
            <p class="text-[28px] font-bold leading-[1.3] text-[#1a2233]">{{ $value }}</p>
        </div>

        @if (isset($icon))
            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#ecfdf3] text-[#15803d] shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>

    @if ($trend)
        <div class="flex items-center gap-1 pt-3 border-t border-[#dfe6ef]">
            @if ($trendDirection === 'up')
                <x-heroicon-m-arrow-trending-up class="h-4 w-4 {{ $trendColorClass }}" />
            @elseif ($trendDirection === 'down')
                <x-heroicon-m-arrow-trending-down class="h-4 w-4 {{ $trendColorClass }}" />
            @endif
            <span class="text-[11px] {{ $trendColorClass }} font-medium">{{ $trend }}</span>
            @if ($trendLabel)
                <span class="text-[11px] text-[#96a2b4]">{{ $trendLabel }}</span>
            @endif
        </div>
    @endif
</div>

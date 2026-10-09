<div {{ $attributes->merge(['class' => 'flex flex-col p-5 rounded-lg bg-white shadow-card']) }}>
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-2">
            <p class="font-body text-text-secondary">{{ $label }}</p>
            <p class="font-h1 text-text-main">{{ $value }}</p>
        </div>

        @if (isset($icon))
            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-primary-50 text-primary shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>

    @if ($trend)
        @php($trendColorClass = $component->trendColor())
        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-border-light">
            @if ($trendDirection === 'up')
                <x-heroicon-m-arrow-trending-up class="h-4 w-4 {{ $trendColorClass }}" />
            @elseif ($trendDirection === 'down')
                <x-heroicon-m-arrow-trending-down class="h-4 w-4 {{ $trendColorClass }}" />
            @endif
            <span class="text-caption {{ $trendColorClass }} font-medium">{{ $trend }}</span>
            @if ($trendLabel)
                <span class="text-caption text-text-muted">{{ $trendLabel }}</span>
            @endif
        </div>
    @endif
</div>

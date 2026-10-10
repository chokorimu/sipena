<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-6 text-center']) }}>
    {{-- Icon --}}
    @if (isset($icon))
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-[#ecfdf3] text-[#15803d] mb-4">
            {{ $icon }}
        </div>
    @else
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-[#ecfdf3] text-[#15803d] mb-4">
            <x-heroicon-o-inbox class="h-8 w-8" />
        </div>
    @endif

    {{-- Title --}}
    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233] mb-1">{{ $title }}</h3>

    {{-- Description --}}
    @if ($description)
        <p class="text-[13px] leading-[1.55] text-[#66758c] max-w-sm">{{ $description }}</p>
    @endif

    {{-- Action --}}
    @if ($actionLabel)
        <div class="mt-4">
            <x-button variant="primary" size="md" :href="$actionHref">
                {{ $actionLabel }}
            </x-button>
        </div>
    @elseif (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>

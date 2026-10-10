@php
    $iconName = $iconNameValue;
@endphp

<div
    {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-[8px] {$variantClass}"]) }}
    @if ($dismissible) x-data="{ visible: true }" x-show="visible" x-transition @endif
>
    {{-- Icon --}}
    <span class="shrink-0">
        @switch($iconName)
            @case('check-circle')
                <x-heroicon-m-check-circle class="h-5 w-5" />
                @break
            @case('exclamation-triangle')
                <x-heroicon-m-exclamation-triangle class="h-5 w-5" />
                @break
            @case('x-circle')
                <x-heroicon-m-x-circle class="h-5 w-5" />
                @break
            @default
                <x-heroicon-m-information-circle class="h-5 w-5" />
        @endswitch
    </span>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="text-[13px] font-semibold leading-[1.45]">{{ $title }}</p>
        @endif
        <div class="text-[12px] leading-[1.5] {{ $title ? 'mt-0.5' : '' }}">
            {{ $slot }}
        </div>
    </div>

    {{-- Dismiss Button --}}
    @if ($dismissible)
        <button
            type="button"
            x-on:click="visible = false"
            class="shrink-0 p-0.5 rounded opacity-60 hover:opacity-100 transition-opacity duration-150"
        >
            <x-heroicon-m-x-mark class="h-4 w-4" />
        </button>
    @endif
</div>

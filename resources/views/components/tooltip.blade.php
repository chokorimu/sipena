<div
    x-data="{ show: false }"
    x-on:mouseenter="show = true"
    x-on:mouseleave="show = false"
    class="relative inline-flex"
    {{ $attributes }}
>
    {{-- Trigger --}}
    {{ $slot }}

    {{-- Tooltip --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute z-50 {{ $positionClass }}"
    >
        <div class="px-2.5 py-1.5 rounded-[4px] bg-[#1a2233] text-white text-[11px] leading-[1.6] whitespace-nowrap shadow-lg">
            {{ $text }}
        </div>
        {{-- Arrow --}}
        <div class="absolute w-0 h-0 {{ $arrowClass }} {{ $arrowBorderClass }}"></div>
    </div>
</div>

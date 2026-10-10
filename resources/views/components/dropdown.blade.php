<div x-data="{ open: false }" class="relative inline-block" {{ $attributes }}>
    {{-- Trigger --}}
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Menu --}}
    <div
        x-show="open"
        x-on:click.outside="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute z-40 mt-1 {{ $width }} {{ $alignClass }} rounded-[8px] bg-white shadow-dropdown border border-[#dfe6ef] p-1"
    >
        {{ $slot }}
    </div>
</div>

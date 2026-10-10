{{-- Modal Overlay --}}
<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    x-on:open-modal.window="if ($event.detail === $el.id) open = true"
    x-on:close-modal.window="if ($event.detail === $el.id) open = false"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    {{ $attributes }}
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50"
        @if ($dismissible) x-on:click="open = false" @endif
    ></div>

    {{-- Modal Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full {{ $sizeClass }} bg-white rounded-[12px] shadow-modal"
            x-on:click.stop
        >
            {{-- Header --}}
            @if ($title || $dismissible)
                <div class="flex items-center justify-between px-5 py-4 border-b border-[#dfe6ef]">
                    @if ($title)
                        <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">{{ $title }}</h3>
                    @else
                        <div></div>
                    @endif

                    @if ($dismissible)
                        <button
                            type="button"
                            x-on:click="open = false"
                            class="p-1 rounded-md text-[#96a2b4] hover:text-[#1a2233] hover:bg-[#f3f6fa] transition-colors duration-150"
                        >
                            <x-heroicon-m-x-mark class="h-5 w-5" />
                        </button>
                    @endif
                </div>
            @endif

            {{-- Body --}}
            <div class="px-5 py-4">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

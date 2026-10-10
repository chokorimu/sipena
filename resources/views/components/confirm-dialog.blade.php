<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    x-on:open-confirm.window="if ($event.detail === $el.id) open = true"
    x-on:close-confirm.window="if ($event.detail === $el.id) open = false"
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
    ></div>

    {{-- Dialog Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white rounded-[12px] shadow-modal p-6"
            x-on:click.stop
        >
            {{-- Close Button --}}
            <button
                type="button"
                x-on:click="open = false"
                class="absolute top-4 right-4 p-1 rounded text-[#96a2b4] hover:text-[#1a2233] transition-colors duration-150"
            >
                <x-heroicon-m-x-mark class="h-5 w-5" />
            </button>

            {{-- Icon --}}
            <div class="flex items-center justify-center h-12 w-12 rounded-full {{ $iconBgClass }} {{ $iconColorClass }} mb-4">
                @switch($variant)
                    @case('danger')
                        <x-heroicon-m-exclamation-triangle class="h-6 w-6" />
                        @break
                    @case('warning')
                        <x-heroicon-m-exclamation-triangle class="h-6 w-6" />
                        @break
                    @default
                        <x-heroicon-m-information-circle class="h-6 w-6" />
                @endswitch
            </div>

            {{-- Title --}}
            <h3 class="text-[22px] font-semibold leading-[1.3] text-[#1a2233] mb-2">{{ $title }}</h3>

            {{-- Message --}}
            @if ($message)
                <p class="text-[13px] leading-[1.55] text-[#66758c] mb-6">{{ $message }}</p>
            @else
                <div class="text-[13px] leading-[1.55] text-[#66758c] mb-6">
                    {{ $slot }}
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <x-button
                    variant="secondary"
                    size="md"
                    x-on:click="open = false"
                >
                    {{ $cancelLabel }}
                </x-button>

                <x-button
                    :variant="$confirmVariant"
                    size="md"
                >
                    {{ $confirmLabel }}
                </x-button>
            </div>
        </div>
    </div>
</div>

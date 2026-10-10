<div
    x-data="{ show: true }"
    x-init="@if ($duration > 0) setTimeout(() => show = false, {{ $duration }}) @endif"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-8"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 w-full max-w-sm bg-white rounded-[8px] shadow-modal border border-[#dfe6ef] p-4']) }}
>
    {{-- Dot Indicator --}}
    <span class="shrink-0 mt-1.5 h-2 w-2 rounded-full {{ $dotColorClass }}"></span>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="text-[13px] font-semibold leading-[1.45] text-[#1a2233]">{{ $title }}</p>
        @endif
        @if ($message)
            <p class="text-[11px] leading-[1.6] text-[#66758c] {{ $title ? 'mt-0.5' : '' }}">{{ $message }}</p>
        @endif
        {{ $slot }}
    </div>

    {{-- Close Button --}}
    <button
        type="button"
        x-on:click="show = false"
        class="shrink-0 p-0.5 rounded text-[#96a2b4] hover:text-[#1a2233] transition-colors duration-150"
    >
        <x-heroicon-m-x-mark class="h-4 w-4" />
    </button>
</div>

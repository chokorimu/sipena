<div {{ $attributes->merge(['class' => 'bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]']) }}>
    {{-- Header --}}
    @if ($title || isset($header))
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#dfe6ef]">
            <div class="flex items-center gap-3">
                @if (isset($headerIcon))
                    <span class="text-[#15803d] shrink-0">{{ $headerIcon }}</span>
                @endif
                @if ($title)
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">{{ $title }}</h3>
                @endif
                @if (isset($header))
                    {{ $header }}
                @endif
            </div>

            @if (isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    {{-- Body --}}
    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    @if (isset($footer))
        <div class="flex items-center justify-between px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
            {{ $footer }}
        </div>
    @endif
</div>

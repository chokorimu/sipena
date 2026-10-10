<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}>
    <span class="relative inline-flex">
        <span class="inline-block rounded-full {{ $dotColorClass }} {{ $dotSizeClass }}"></span>
        @if ($pulse)
            <span class="absolute inline-flex h-full w-full rounded-full {{ $dotColorClass }} opacity-75 animate-ping"></span>
        @endif
    </span>

    @if ($label)
        <span class="text-[12px] leading-[1.5] text-[#1a2233]">{{ $label }}</span>
    @endif
</span>

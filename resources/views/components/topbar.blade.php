<header {{ $attributes->merge(['class' => 'flex items-center justify-between bg-white px-6 py-4 border-b border-[#dfe6ef]']) }}>
    <div class="flex items-center gap-4">
        @if ($title)
            <h1 class="text-[18px] font-semibold leading-[1.35] text-[#1a2233]">{{ $title }}</h1>
        @endif
        {{ $slot }}
    </div>

    <div class="flex items-center gap-4">
        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</header>

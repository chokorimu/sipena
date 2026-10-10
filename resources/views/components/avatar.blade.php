@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $name }}"
        {{ $attributes->merge(['class' => "inline-block rounded-full object-cover {$sizeClass}"]) }}
    />
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-[#ecfdf3] text-[#15803d] font-semibold {$sizeClass}"]) }}>
        {{ $initialsText }}
    </span>
@endif

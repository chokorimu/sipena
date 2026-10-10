@error($name)
    <p {{ $attributes->merge(['class' => 'text-[11px] leading-[1.6] text-[#c13e45]']) }}>{{ $message }}</p>
@enderror

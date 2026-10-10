<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'block text-[12px] font-semibold leading-[1.45] text-[#1a2233]']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-[#c13e45]">*</span>
    @endif
</label>

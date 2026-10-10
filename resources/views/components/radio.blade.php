<label class="inline-flex items-center gap-2 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'h-4 w-4 border-[#dfe6ef] text-[#15803d] focus:ring-2 focus:ring-[#15803d]/30 focus:ring-offset-0 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed']) }}
        @checked(old($name) === $value || $checked)
        @disabled($disabled)
    />
    @if ($label)
        <span class="text-[13px] leading-[1.45] text-[#1a2233] select-none">{{ $label }}</span>
    @endif
</label>

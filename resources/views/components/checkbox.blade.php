<label class="inline-flex items-center gap-2 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value ?? '1' }}"
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded-sm border-border text-primary focus:ring-2 focus:ring-primary-300 focus:ring-offset-0 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed']) }}
        @checked(old($name, $checked))
        @disabled($disabled)
    />
    @if ($label)
        <span class="text-body text-text-main select-none">{{ $label }}</span>
    @endif
</label>

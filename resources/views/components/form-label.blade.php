<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'block text-body font-medium text-text-main mb-1']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if ($label)
        <x-form-label :for="$for" :required="$required">{{ $label }}</x-form-label>
    @endif

    {{ $slot }}

    @if ($error)
        <p class="mt-1 text-[11px] leading-[1.6] text-[#c13e45]">{{ $error }}</p>
    @elseif ($helper)
        <p class="mt-1 text-[11px] leading-[1.6] text-[#66758c]">{{ $helper }}</p>
    @endif

    @if ($for)
        <x-form-error :name="$for" />
    @endif
</div>

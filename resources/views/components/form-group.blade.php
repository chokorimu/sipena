<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if ($label)
        <x-form-label :for="$for" :required="$required">{{ $label }}</x-form-label>
    @endif

    {{ $slot }}

    @if ($error)
        <p class="mt-1 text-caption text-danger">{{ $error }}</p>
    @elseif ($helper)
        <p class="mt-1 text-caption text-text-secondary">{{ $helper }}</p>
    @endif

    @if ($for)
        <x-form-error :name="$for" />
    @endif
</div>

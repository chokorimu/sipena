@php
    $selectClasses = 'block w-full rounded-md border bg-white px-3 py-2 text-body text-text-main transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-0 appearance-none bg-no-repeat bg-right pr-10 disabled:opacity-50 disabled:cursor-not-allowed';
    $borderClass = $component->hasError() ? 'border-danger focus:border-danger focus:ring-danger-200' : 'border-border focus:border-secondary focus:ring-secondary-200';
    $chevronBg = "bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236C757D' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\")] bg-[length:16px_16px] bg-[position:right_12px_center]";
@endphp

<div>
    @if ($label)
        <x-form-label :for="$name" :required="$required">{{ $label }}</x-form-label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => "{$selectClasses} {$borderClass} {$chevronBg}"]) }}
        @required($required)
        @disabled($disabled)
    >
        @if ($placeholder)
            <option value="" disabled @selected(!old($name, $value))>{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach

        {{ $slot }}
    </select>

    @if ($helper && !$component->hasError())
        <p class="mt-1 text-caption text-text-secondary">{{ $helper }}</p>
    @endif

    <x-form-error :name="$name" />
</div>

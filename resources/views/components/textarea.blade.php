@php
    $textareaClasses = 'block w-full rounded-md border bg-white px-3 py-2 text-body text-text-main placeholder:text-text-muted transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-0 resize-y disabled:opacity-50 disabled:cursor-not-allowed';
    $borderClass = $component->hasError() ? 'border-danger focus:border-danger focus:ring-danger-200' : 'border-border focus:border-secondary focus:ring-secondary-200';
@endphp

<div>
    @if ($label)
        <x-form-label :for="$name" :required="$required">{{ $label }}</x-form-label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => "{$textareaClasses} {$borderClass}"]) }}
        @required($required)
        @disabled($disabled)
    >{{ old($name, $value) }}</textarea>

    @if ($helper && !$component->hasError())
        <p class="mt-1 text-caption text-text-secondary">{{ $helper }}</p>
    @endif

    <x-form-error :name="$name" />
</div>

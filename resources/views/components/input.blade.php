@php
    $inputClasses = 'block w-full h-[42px] rounded-[8px] border bg-white px-3 text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:bg-[#e9edf3] disabled:text-[#96a2b4] disabled:cursor-not-allowed';
    $borderClass = $hasErrorState ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20';
@endphp

<div class="flex flex-col gap-[7px]">
    @if ($label)
        <x-form-label :for="$name" :required="$required">{{ $label }}</x-form-label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => "{$inputClasses} {$borderClass}"]) }}
        @required($required)
        @disabled($disabled)
    />

    @if ($helper && !$hasErrorState)
        <p class="text-[11px] leading-[1.6] text-[#66758c]">{{ $helper }}</p>
    @endif

    <x-form-error :name="$name" />
</div>

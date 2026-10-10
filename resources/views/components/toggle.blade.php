<label class="inline-flex items-center gap-3 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <button
        type="button"
        role="switch"
        aria-checked="{{ $checked ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => 'relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#15803d]/30 focus:ring-offset-2 ' . ($checked ? 'bg-[#15803d]' : 'bg-[#dfe6ef]')]) }}
        @disabled($disabled)
        onclick="
            const input = this.nextElementSibling;
            const isChecked = this.getAttribute('aria-checked') === 'true';
            this.setAttribute('aria-checked', !isChecked);
            this.classList.toggle('bg-[#15803d]', !isChecked);
            this.classList.toggle('bg-[#dfe6ef]', isChecked);
            this.querySelector('span').classList.toggle('translate-x-5', !isChecked);
            this.querySelector('span').classList.toggle('translate-x-0', isChecked);
            input.value = !isChecked ? '1' : '0';
        "
    >
        <span
            aria-hidden="true"
            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-md transition duration-200 ease-in-out {{ $checked ? 'translate-x-5' : 'translate-x-0' }}"
        ></span>
    </button>
    <input type="hidden" name="{{ $name }}" value="{{ $checked ? '1' : '0' }}" />
    @if ($label)
        <span class="text-[13px] leading-[1.45] text-[#1a2233] select-none">{{ $label }}</span>
    @endif
</label>

<div {{ $attributes->merge(['class' => 'border-b border-[#dfe6ef]']) }}>
    <nav class="flex gap-0 -mb-px" role="tablist">
        {{ $slot }}
    </nav>
</div>

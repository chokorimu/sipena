<div {{ $attributes->merge(['class' => 'relative']) }}>
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#96a2b4]">
        <x-heroicon-m-magnifying-glass class="h-4.5 w-4.5" />
    </span>

    <input
        type="search"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="block w-full h-[42px] rounded-[8px] border border-[#dfe6ef] bg-white pl-10 pr-3 py-2 text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:border-[#15803d] focus:ring-[#15803d]/20"
    />
</div>

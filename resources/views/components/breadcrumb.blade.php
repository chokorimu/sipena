<nav {{ $attributes->merge(['class' => 'flex items-center']) }} aria-label="Breadcrumb">
    <ol class="flex items-center gap-1.5 text-[12px] leading-[1.5] text-[#66758c]">
        {{ $slot }}
    </ol>
</nav>

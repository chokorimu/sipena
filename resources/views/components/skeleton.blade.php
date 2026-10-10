@for ($i = 0; $i < $count; $i++)
    <div
        {{ $attributes->merge(['class' => "animate-pulse bg-[#e9edf3] {$shapeClass} {$dimensionClass}"]) }}
    ></div>
@endfor

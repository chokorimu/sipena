@error($name)
    <p {{ $attributes->merge(['class' => 'mt-1 text-caption text-danger']) }}>{{ $message }}</p>
@enderror

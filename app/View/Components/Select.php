<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    /**
     * @param  array<string, string>  $options
     */
    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $value = null,
        public array $options = [],
        public bool $required = false,
        public bool $disabled = false,
    ) {}

    public function hasError(): bool
    {
        return session('errors')?->has($this->name) ?? false;
    }

    public function render(): View|Closure|string
    {
        return view('components.select');
    }
}

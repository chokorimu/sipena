<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * @param  'text'|'email'|'password'|'number'|'tel'|'url'|'search'|'date'  $type
     */
    public function __construct(
        public string $name,
        public string $type = 'text',
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $value = null,
        public bool $required = false,
        public bool $disabled = false,
    ) {}

    public function hasError(): bool
    {
        return session('errors')?->has($this->name) ?? false;
    }

    public function render(): View|Closure|string
    {
        return view('components.input', [
            'hasErrorState' => $this->hasError(),
        ]);
    }
}

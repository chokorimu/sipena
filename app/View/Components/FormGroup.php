<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormGroup extends Component
{
    public function __construct(
        public ?string $label = null,
        public ?string $for = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $required = false,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.form-group');
    }
}

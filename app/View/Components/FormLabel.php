<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormLabel extends Component
{
    public function __construct(
        public ?string $for = null,
        public bool $required = false,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.form-label');
    }
}

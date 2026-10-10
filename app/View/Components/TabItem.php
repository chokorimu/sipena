<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TabItem extends Component
{
    public function __construct(
        public string $label = '',
        public bool $active = false,
        public ?string $href = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.tab-item');
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BreadcrumbItem extends Component
{
    public function __construct(
        public string $label,
        public ?string $href = null,
        public bool $active = false,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.breadcrumb-item');
    }
}

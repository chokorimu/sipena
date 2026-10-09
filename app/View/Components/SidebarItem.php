<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarItem extends Component
{
    public function __construct(
        public string $label,
        public string $route = '#',
        public ?string $icon = null,
        public bool $active = false,
    ) {}

    public function isActive(): bool
    {
        if ($this->active) {
            return true;
        }

        if ($this->route !== '#' && request()->routeIs($this->route)) {
            return true;
        }

        return false;
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar-item');
    }
}

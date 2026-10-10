<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmptyState extends Component
{
    public function __construct(
        public string $title = 'Tidak ada data',
        public ?string $description = null,
        public ?string $actionLabel = null,
        public ?string $actionHref = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.empty-state');
    }
}

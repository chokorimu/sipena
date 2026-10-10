<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchInput extends Component
{
    public function __construct(
        public string $name = 'search',
        public string $placeholder = 'Cari...',
        public ?string $value = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.search-input');
    }
}

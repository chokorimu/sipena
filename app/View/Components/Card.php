<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * @param  bool  $padding  Apakah body card memiliki padding default
     */
    public function __construct(
        public ?string $title = null,
        public bool $padding = true,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}

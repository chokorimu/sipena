<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tab extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.tab');
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownItem extends Component
{
    /**
     * @param  bool  $danger  Apakah item berwarna merah (hapus, dll)
     * @param  bool  $divider  Tampilkan garis pemisah di atas item
     */
    public function __construct(
        public ?string $href = null,
        public bool $danger = false,
        public bool $divider = false,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.dropdown-item');
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * @param  'left'|'right'  $align  Posisi dropdown menu relatif terhadap trigger
     * @param  string|null  $width  Lebar dropdown (class Tailwind, e.g. 'w-48')
     */
    public function __construct(
        public string $align = 'right',
        public string $width = 'w-[208px]',
    ) {}

    public function alignmentClasses(): string
    {
        return match ($this->align) {
            'left' => 'left-0',
            'right' => 'right-0',
            default => 'right-0',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.dropdown', [
            'alignClass' => $this->alignmentClasses(),
        ]);
    }
}

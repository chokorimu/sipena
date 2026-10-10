<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tooltip extends Component
{
    /**
     * @param  string  $text  Teks tooltip
     * @param  'top'|'bottom'|'left'|'right'  $position  Posisi tooltip
     */
    public function __construct(
        public string $text = '',
        public string $position = 'top',
    ) {}

    public function positionClasses(): string
    {
        return match ($this->position) {
            'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
            'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
            'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
            'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
            default => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        };
    }

    public function arrowClasses(): string
    {
        return match ($this->position) {
            'top' => 'top-full left-1/2 -translate-x-1/2 border-t-[#1a2233]',
            'bottom' => 'bottom-full left-1/2 -translate-x-1/2 border-b-[#1a2233]',
            'left' => 'left-full top-1/2 -translate-y-1/2 border-l-[#1a2233]',
            'right' => 'right-full top-1/2 -translate-y-1/2 border-r-[#1a2233]',
            default => 'top-full left-1/2 -translate-x-1/2 border-t-[#1a2233]',
        };
    }

    public function arrowBorderClasses(): string
    {
        return match ($this->position) {
            'top' => 'border-l-transparent border-r-transparent border-b-transparent border-t-4 border-l-4 border-r-4',
            'bottom' => 'border-l-transparent border-r-transparent border-t-transparent border-b-4 border-l-4 border-r-4',
            'left' => 'border-t-transparent border-b-transparent border-r-transparent border-l-4 border-t-4 border-b-4',
            'right' => 'border-t-transparent border-b-transparent border-l-transparent border-r-4 border-t-4 border-b-4',
            default => 'border-l-transparent border-r-transparent border-b-transparent border-t-4 border-l-4 border-r-4',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.tooltip', [
            'positionClass' => $this->positionClasses(),
            'arrowClass' => $this->arrowClasses(),
            'arrowBorderClass' => $this->arrowBorderClasses(),
        ]);
    }
}

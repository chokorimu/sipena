<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Spinner extends Component
{
    /**
     * @param  'sm'|'md'|'lg'  $size
     * @param  'primary'|'white'|'secondary'  $color
     */
    public function __construct(
        public string $size = 'md',
        public string $color = 'primary',
        public ?string $label = null,
    ) {}

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-4 w-4',
            'md' => 'h-6 w-6',
            'lg' => 'h-8 w-8',
            default => 'h-6 w-6',
        };
    }

    public function colorClasses(): string
    {
        return match ($this->color) {
            'primary' => 'text-[#15803d]',
            'white' => 'text-white',
            'secondary' => 'text-[#66758c]',
            default => 'text-[#15803d]',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.spinner', [
            'sizeClass' => $this->sizeClasses(),
            'colorClass' => $this->colorClasses(),
        ]);
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    /**
     * @param  'primary'|'secondary'|'success'|'warning'|'danger'|'info'|'neutral'  $variant
     * @param  'sm'|'md'  $size
     */
    public function __construct(
        public string $variant = 'primary',
        public string $size = 'md',
    ) {}

    public function variantClasses(): string
    {
        return match ($this->variant) {
            'primary' => 'bg-[#ecfdf3] text-[#15803d]',
            'secondary' => 'bg-[#f3f6fa] text-[#66758c]',
            'success' => 'bg-[#ecfdf3] text-[#15803d]',
            'warning' => 'bg-[#fff5df] text-[#a56708]',
            'danger' => 'bg-[#fff0f0] text-[#c13e45]',
            'info' => 'bg-[#f0f4ff] text-[#3b5998]',
            'neutral' => 'bg-[#f3f6fa] text-[#66758c]',
            default => 'bg-[#ecfdf3] text-[#15803d]',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'px-2 py-0.5 text-[10px]',
            'md' => 'px-[10px] py-1 text-[11px]',
            default => 'px-[10px] py-1 text-[11px]',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.badge', [
            'variantClass' => $this->variantClasses(),
            'sizeClass' => $this->sizeClasses(),
        ]);
    }
}

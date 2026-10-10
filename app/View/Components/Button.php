<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    /**
     * @param  'primary'|'secondary'|'outline'|'danger'|'ghost'  $variant
     * @param  'sm'|'md'|'lg'  $size
     * @param  'button'|'submit'|'reset'  $type
     */
    public function __construct(
        public string $variant = 'primary',
        public string $size = 'md',
        public string $type = 'button',
        public ?string $href = null,
        public bool $disabled = false,
    ) {}

    public function variantClasses(): string
    {
        return match ($this->variant) {
            'primary' => 'bg-[#15803d] border border-[#15803d] text-white hover:bg-[#0f8a4b] active:bg-[#0e6b3d] focus:shadow-[0px_0px_0px_3px_#a7f3d0]',
            'secondary' => 'bg-white border border-[#dfe6ef] text-[#15803d] hover:bg-[#ecfdf3] active:bg-[#d1fae5] focus:shadow-[0px_0px_0px_3px_#a7f3d0]',
            'outline' => 'bg-white border border-white text-[#1a2233] hover:bg-[#f3f6fa] active:bg-[#e9edf3] focus:shadow-[0px_0px_0px_3px_#e2e8f0]',
            'danger' => 'bg-[#c13e45] border border-[#c13e45] text-white hover:bg-[#a83239] active:bg-[#8e2a30] focus:shadow-[0px_0px_0px_3px_#fecaca]',
            'ghost' => 'bg-transparent border border-transparent text-[#1a2233] hover:bg-[#f3f6fa] active:bg-[#e9edf3] focus:shadow-[0px_0px_0px_3px_#e2e8f0]',
            default => 'bg-[#15803d] border border-[#15803d] text-white hover:bg-[#0f8a4b] active:bg-[#0e6b3d] focus:shadow-[0px_0px_0px_3px_#a7f3d0]',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-8 px-3 text-[12px] rounded-[8px] gap-2',
            'md' => 'h-10 px-4 text-[13px] rounded-[8px] gap-2',
            'lg' => 'h-12 px-4 text-[13px] rounded-[8px] gap-2',
            default => 'h-10 px-4 text-[13px] rounded-[8px] gap-2',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.button', [
            'variantClass' => $this->variantClasses(),
            'sizeClass' => $this->sizeClasses(),
        ]);
    }
}

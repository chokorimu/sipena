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
            'primary' => 'bg-primary text-white hover:bg-primary-700 active:bg-primary-800 focus:ring-primary-300',
            'secondary' => 'bg-secondary text-white hover:bg-secondary-500 active:bg-secondary-600 focus:ring-secondary-200',
            'outline' => 'bg-transparent text-primary border border-primary hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-200',
            'danger' => 'bg-danger text-white hover:bg-danger-600 active:bg-danger-700 focus:ring-danger-300',
            'ghost' => 'bg-transparent text-text-main hover:bg-neutral-100 active:bg-neutral-200 focus:ring-neutral-300',
            default => 'bg-primary text-white hover:bg-primary-700 active:bg-primary-800 focus:ring-primary-300',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'px-3 py-1.5 text-caption rounded-sm gap-1',
            'md' => 'px-4 py-2 text-body rounded-md gap-1.5',
            'lg' => 'px-6 py-3 text-body rounded-md gap-2',
            default => 'px-4 py-2 text-body rounded-md gap-1.5',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}

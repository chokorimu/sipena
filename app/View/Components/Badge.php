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
            'primary' => 'bg-primary-50 text-primary-700',
            'secondary' => 'bg-secondary-50 text-secondary-700',
            'success' => 'bg-success-50 text-success-700',
            'warning' => 'bg-warning-50 text-warning-700',
            'danger' => 'bg-danger-50 text-danger-700',
            'info' => 'bg-info-50 text-info-700',
            'neutral' => 'bg-neutral-100 text-neutral-700',
            default => 'bg-primary-50 text-primary-700',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'px-1.5 py-0.5 text-small',
            'md' => 'px-2 py-0.5 text-caption',
            default => 'px-2 py-0.5 text-caption',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.badge');
    }
}

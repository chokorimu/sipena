<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProgressBar extends Component
{
    /**
     * @param  int|float  $value  Nilai progress (0-100)
     * @param  'primary'|'success'|'warning'|'danger'|'info'  $variant
     * @param  'sm'|'md'|'lg'  $size
     * @param  bool  $showLabel  Tampilkan persentase di samping bar
     */
    public function __construct(
        public int|float $value = 0,
        public string $variant = 'primary',
        public string $size = 'md',
        public bool $showLabel = false,
        public ?string $label = null,
    ) {}

    public function barColorClasses(): string
    {
        return match ($this->variant) {
            'primary' => 'bg-[#15803d]',
            'success' => 'bg-[#15803d]',
            'warning' => 'bg-[#a56708]',
            'danger' => 'bg-[#c13e45]',
            'info' => 'bg-[#3b5998]',
            default => 'bg-[#15803d]',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-1.5',
            'md' => 'h-2.5',
            'lg' => 'h-4',
            default => 'h-2.5',
        };
    }

    public function clampedValue(): int|float
    {
        return max(0, min(100, $this->value));
    }

    public function render(): View|Closure|string
    {
        return view('components.progress-bar', [
            'clamped' => $this->clampedValue(),
            'barColorClass' => $this->barColorClasses(),
            'sizeClass' => $this->sizeClasses(),
        ]);
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * @param  'success'|'warning'|'danger'|'info'  $variant
     * @param  bool  $dismissible  Apakah bisa ditutup
     */
    public function __construct(
        public string $variant = 'info',
        public ?string $title = null,
        public bool $dismissible = false,
    ) {}

    public function variantClasses(): string
    {
        return match ($this->variant) {
            'success' => 'bg-[#ecfdf3] text-[#15803d]',
            'warning' => 'bg-[#fff5df] text-[#a56708]',
            'danger' => 'bg-[#fff0f0] text-[#c13e45]',
            'info' => 'bg-[#f0f4ff] text-[#3b5998]',
            default => 'bg-[#f0f4ff] text-[#3b5998]',
        };
    }

    public function iconName(): string
    {
        return match ($this->variant) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger' => 'x-circle',
            'info' => 'information-circle',
            default => 'information-circle',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.alert', [
            'variantClass' => $this->variantClasses(),
            'iconNameValue' => $this->iconName(),
        ]);
    }
}

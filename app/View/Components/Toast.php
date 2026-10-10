<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toast extends Component
{
    /**
     * @param  'success'|'error'|'warning'|'info'  $variant
     * @param  int  $duration  Durasi auto-dismiss dalam milidetik (0 = tidak auto-dismiss)
     */
    public function __construct(
        public string $variant = 'info',
        public ?string $title = null,
        public ?string $message = null,
        public int $duration = 5000,
    ) {}

    public function dotColorClasses(): string
    {
        return match ($this->variant) {
            'success' => 'bg-[#15803d]',
            'error' => 'bg-[#c13e45]',
            'warning' => 'bg-[#a56708]',
            'info' => 'bg-[#3b5998]',
            default => 'bg-[#3b5998]',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.toast', [
            'dotColorClass' => $this->dotColorClasses(),
        ]);
    }
}

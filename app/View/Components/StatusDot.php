<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusDot extends Component
{
    /**
     * @param  'active'|'inactive'|'warning'|'processing'  $status
     * @param  'sm'|'md'  $size
     * @param  bool  $pulse  Animasi pulse (cocok untuk status "processing")
     */
    public function __construct(
        public string $status = 'active',
        public string $size = 'md',
        public ?string $label = null,
        public bool $pulse = false,
    ) {}

    public function dotColorClasses(): string
    {
        return match ($this->status) {
            'active' => 'bg-[#15803d]',
            'inactive' => 'bg-[#96a2b4]',
            'warning' => 'bg-[#a56708]',
            'processing' => 'bg-[#3b5998]',
            default => 'bg-[#96a2b4]',
        };
    }

    public function dotSizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-2 w-2',
            'md' => 'h-2.5 w-2.5',
            default => 'h-2.5 w-2.5',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.status-dot', [
            'dotColorClass' => $this->dotColorClasses(),
            'dotSizeClass' => $this->dotSizeClasses(),
        ]);
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    /**
     * @param  'sm'|'md'|'lg'  $size
     */
    public function __construct(
        public string $name = '',
        public ?string $src = null,
        public string $size = 'md',
    ) {}

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));

        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-8 w-8 text-small',
            'md' => 'h-10 w-10 text-caption',
            'lg' => 'h-12 w-12 text-body',
            default => 'h-10 w-10 text-caption',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.avatar');
    }
}

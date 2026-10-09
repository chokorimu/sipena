<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardStat extends Component
{
    /**
     * @param  'up'|'down'|null  $trendDirection
     */
    public function __construct(
        public string $label = '',
        public string|int $value = 0,
        public ?string $trend = null,
        public ?string $trendDirection = null,
        public ?string $trendLabel = null,
    ) {}

    public function trendColor(): string
    {
        return match ($this->trendDirection) {
            'up' => 'text-success',
            'down' => 'text-danger',
            default => 'text-text-secondary',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.card-stat');
    }
}

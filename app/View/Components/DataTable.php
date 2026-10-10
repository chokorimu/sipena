<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    /**
     * @param  array<int, array<string, mixed>>  $rows  Data baris tabel
     * @param  bool  $striped  Apakah baris bergantian warna
     * @param  bool  $hoverable  Apakah baris punya hover effect
     */
    public function __construct(
        public array $rows = [],
        public bool $striped = true,
        public bool $hoverable = true,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.data-table');
    }
}

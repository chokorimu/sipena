@extends('layout.app')

@section('title', 'Dashboard - SIPENA')

@section('content')
<div class="flex flex-col gap-3 p-6">
    <h1 class="font-display text-main">Dashboard</h1>
    <div class="flex gap-3">

        <x-card-stat />
        <x-card-stat />
        <x-card-stat />
        <x-card-stat />
    </div>
</div>
@endsection
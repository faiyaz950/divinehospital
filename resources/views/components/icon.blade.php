@props(['name'])
@php($filled = in_array($name, \App\Support\Icons::FILLED, true))
<svg {{ $attributes->class('icon') }} viewBox="0 0 24 24" @if ($filled) fill="currentColor" @else fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" @endif aria-hidden="true" focusable="false">{!! \App\Support\Icons::svg($name) !!}</svg>

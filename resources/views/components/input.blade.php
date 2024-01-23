@props(['disabled' => false])
@php
    $attributes = $attributes->merge(['class' => 'block w-full rounded-lg border-gray-200']);
    if ($attributes['type'] == 'radio') $attributes['class'] = 'h-4 w-4 border-gray-200';
@endphp
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes !!}>

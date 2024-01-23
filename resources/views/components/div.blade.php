@props(['value'])
<div {{ $attributes->merge(['class' => 'text-gray-400']) }}>
    {{ $value ?? $slot }}
</div>

@props(['submit'])

<div {{ $attributes->merge(['class' => 'p-12 rounded-2xl bg-white space-y-6']) }}>

    {{ $preform ?? '' }}

    <form wire:submit.prevent="{{ $submit }}">
        {{ $form }}

        @if (isset($actions))
            {{ $actions }}
        @endif
    </form>

    {{ $postform ?? '' }}
</div>

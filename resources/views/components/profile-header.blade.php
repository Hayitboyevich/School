@php($user = auth()->user())

<div class="space-y-2">
    <h4 class="text-xl font-semibold text-center">
        {{ $user->name }}
    </h4>

    <div class="text-center text-gray-500">
        Школа Sehriyo
    </div>
</div>

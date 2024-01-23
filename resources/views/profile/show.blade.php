<x-app-layout>
    <div class="max-w-md mx-auto my-12">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
        @endif
    </div>
</x-app-layout>

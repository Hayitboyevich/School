<x-app-layout>
    <x-authentication-card>
        <x-slot name="title">
            {{ __('Password changes') }}
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="mt-4">
                <x-label for="current_password" value="{{ __('Current Password') }}" />
                <x-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autocomplete="current_password" />
            </div>

            <div class="mt-4">
                <x-label for="new_password" value="{{ __('New Password') }}" />
                <x-input id="new_password" class="block mt-1 w-full" type="password" name="new_password" required autocomplete="new_password" />
            </div>

            <div class="mt-4">
                <x-label for="new_password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="new_password_confirmation" class="block mt-1 w-full" type="password" name="new_password_confirmation" required autocomplete="new_password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-app-layout>

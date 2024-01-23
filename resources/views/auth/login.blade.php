<x-app-layout>
    <x-authentication-card>
        <x-slot name="title">
            {{ __('Войти') }}
        </x-slot>

        <x-validation-errors class="mb-4"/>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-6">
                <div class="space-y-2">
                    <x-label for="phone" value="{{ __('Номер телефона') }}"/>
                    <x-input id="phone" type="text" name="phone" :value="old('phone', '998')" required autofocus autocomplete="phone"/>
                </div>
                <div class="space-y-2">
                    <x-label for="password" value="{{ __('Пароль') }}"/>
                    <x-input id="phone" type="password" name="password" required autocomplete="current-password"/>
                </div>
                <div class="block mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" class="form-checkbox" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>
                <x-button>
                    {{ __('Войти') }}
                </x-button>
            </div>
        </form>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a class="text-blue-800" href="{{ route('password.request') }}">
                    {{ __('Забыли пароль?') }}
                </a>
            @endif
        </div>
    </x-authentication-card>
    <x-mask-field :fieldId="'phone'"/>
</x-app-layout>

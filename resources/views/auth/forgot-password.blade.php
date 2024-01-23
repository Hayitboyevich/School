<x-app-layout>
    <x-authentication-card>
        <x-slot name="title">
            {{ __('Восстановление пароля') }}
        </x-slot>

        <x-warning-alert>
            {{ __('Если ты ученик, обратись к своему учителю за паролем.') }}
        </x-warning-alert>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="space-y-6">
                <div class="space-y-2">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>
                <x-button>
                    {{ __('Отправить запрос') }}
                </x-button>
            </div>
        </form>

        <div class="text-center">
            <a href="" class="text-blue-800">Войти</a>
        </div>
    </x-authentication-card>
</x-app-layout>

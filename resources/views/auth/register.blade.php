<x-app-layout>
    <x-authentication-card>
        <x-slot name="title">
            {{ __('Регистрация') }}
        </x-slot>

        <x-warning-alert>
            {{ __('Мы просим указывать только достоверные данные при регистрации.') }}
        </x-warning-alert>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="space-y-6">
                <div class="space-y-2">
                    <x-label for="name" value="{{ __('Ваше имя и фамилия ') }}" />
                    <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>
                <div class="space-y-2">
                    <x-label for="city" value="{{ __('Город') }}" />
                    <x-input id="city" type="text" name="city" :value="old('city')" required autofocus autocomplete="city" placeholder="{{ __('Ташкент') }}" />
                </div>
                <div class="space-y-2">
                    <x-label for="school" value="{{ __('Школа') }}" />
                    <x-input id="school" type="text" name="school" :value="old('school')" required autofocus autocomplete="school" placeholder="{{ __('Sehriyo') }}" />
                </div>
                <div class="space-y-2">
                    <x-label for="group" value="{{ __('Kласс') }}" />
                    <x-input id="group" type="text" name="group" :value="old('group')" required autofocus autocomplete="group" placeholder="{{ __('5 A') }}" />
                </div>
                <div class="space-y-2">
                    <x-label for="phone" value="{{ __('Номер телефона') }}"/>
                    <x-input id="phone" type="text" name="phone" :value="old('phone', '998')" required autofocus autocomplete="phone" />
                </div>
                <div class="space-y-2">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input id="password" type="password" name="password" required autocomplete="new-password" />
                </div>
                <div class="space-y-2">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                    <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>
                <x-button>
                    {{ __('Отправить запрос') }}
                </x-button>
            </div>
        </form>

        <div class="text-center">
            Уже есть аккаунт? <a href="{{ route('login') }}" class="text-blue-800">Войти</a>
        </div>
    </x-authentication-card>
    <x-mask-field :fieldId="'phone'"/>
</x-app-layout>

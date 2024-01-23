<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Detailed Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile detailed information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="first_name" value="{{ __('First name') }}"/>
            <x-input id="first_name" type="text" class="mt-1 block w-full" wire:model.defer="state.first_name"
                     autocomplete="first_name"/>
            <x-input-error for="first_name" class="mt-2"/>
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="last_name" value="{{ __('Last name') }}"/>
            <x-input id="last_name" type="text" class="mt-1 block w-full" wire:model.defer="state.last_name"
                     autocomplete="last_name"/>
            <x-input-error for="last_name" class="mt-2"/>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="middle_name" value="{{ __('Middle name') }}"/>
            <x-input id="middle_name" type="text" class="mt-1 block w-full" wire:model.defer="state.middle_name"
                     autocomplete="middle_name"/>
            <x-input-error for="middle_name" class="mt-2"/>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="gender" value="{{ __('Gender') }}"/>
            <select id="gender" class="mt-1 block w-full" wire:model.defer="state.gender" autocomplete="gender">
                <option>Select</option>
                <option value="1" {{ $this->state['gender'] == '1' ? 'selected' : '' }}>male</option>
                <option value="0" {{ $this->state['gender'] == '0' ? 'selected' : '' }}>female</option>
            </select>
            <x-input-error for="gender" class="mt-2"/>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="birth_date" value="{{ __('Birth date') }}"/>
            <x-input id="birth_date" type="date" class="mt-1 block w-full" wire:model.defer="state.birth_date"
                     autocomplete="birth_date" value="{{$this->state['birth_date']}}"/>
            <x-input-error for="birth_date" class="mt-2"/>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="status" value="{{ __('Status') }}"/>
            <select id="status" class="mt-1 block w-full" wire:model.defer="state.status" autocomplete="status">
                <option>Select</option>
                <option value="1" {{ $this->state['status'] == '1' ? 'selected' : '' }}>active</option>
                <option value="0" {{ $this->state['status'] == '0' ? 'selected' : '' }}>inactive</option>
            </select>
            <x-input-error for="status" class="mt-2"/>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="city_id" value="{{ __('City id') }}"/>
            <select id="city_id" class="mt-1 block w-full" wire:model.defer="state.city_id" autocomplete="city_id">
                <option>Select</option>
                @foreach($this->cities as $city)
                    <option value="{{$city->id}}" {{ $this->state['city_id'] == $city->id ? 'selected' : '' }}>{{$city->id}}</option>
                @endforeach
            </select>
            <x-input-error for="city_id" class="mt-2"/>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>

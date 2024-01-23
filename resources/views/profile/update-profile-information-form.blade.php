<x-form-section submit="updateProfileInformation">
    <x-slot name="preform">
        <x-profile-header/>
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())

            <div class="space-y-6 mb-5" x-data="{photoName: null, photoPreview: null}">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                       wire:model="photo"
                       x-ref="photo"
                       x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            "/>

                <!-- Current Profile Photo -->
                <div x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                         class="rounded-full w-24 h-24 mx-auto">
                </div>

                <!-- New Profile Photo Preview -->
                <div x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-24 h-24 mx-auto bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Изменить фото...') }}
                </x-secondary-button>

                <x-input-error for="photo" class="mt-2"/>
            </div>
        @endif

        <div class="space-y-6">
            <div class="space-y-2">
                <x-div value="{{ __('Group') }}"/>
                @if($this->user->groups()->count() > 0)
                <div>{{ $this->user->groups()->first()->group_level }} «{{ $this->user->groups()->first()->group_letter }}»</div>
                @endif
            </div>
            <div class="space-y-2">
                <x-div value="{{ __('Phone number') }}"/>
                <div>{{ format_phone($this->user->phone) }}</div>
            </div>
            <div class="space-y-2">
                <x-div value="{{ __('Email') }}"/>
                <div>{{ $this->user->email }}</div>
            </div>
            <div class="space-y-2">
                <x-div value="{{ __('Birthday') }}"/>
                <div>{{ human_date_iso($this->user->birth_date) }}</div>
            </div>
            <div class="space-y-2">
                <x-div value="{{ __('Password') }}"/>
                <div><a href="{{ route('password.change') }}" class="text-blue-800">{{ __('Change Password') }}</a></div>
            </div>
        </div>

    </x-slot>
    <x-slot name="postform">
        <div class="flex justify-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined">{{ __('Logout') }}</button>
            </form>
        </div>
    </x-slot>
</x-form-section>

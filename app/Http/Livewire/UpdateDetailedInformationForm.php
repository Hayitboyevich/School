<?php

namespace App\Http\Livewire;

use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class UpdateDetailedInformationForm extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    public $cities;

    public function mount()
    {
        $user = Auth::user();

        $this->cities = City::all('id');
        $this->state = $user->withoutRelations()->toArray();
    }

    public function updateProfileInformation()
    {
        $this->resetErrorBag();
        $this->update(Auth::user(), $this->state);
    }

    public function update(User $user, array $input): void
    {
        $validated =  Validator::make($input, [
            'first_name' => ['max:50'],
            'last_name' => ['max:50'],
            'middle_name' => ['max:50'],
            'gender' => [],
            'birth_date' => ['date', 'nullable'],
            'status' => [],
            'city_id' => [],
        ])->validateWithBag('updateDetailedInformation');

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->middle_name = $validated['middle_name'];
        $user->gender = $validated['gender'];
        $user->status = $validated['status'];
        $user->city_id = $validated['city_id'];
        $user->save();
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.update-detailed-information-form');
    }
}

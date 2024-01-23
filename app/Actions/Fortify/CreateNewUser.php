<?php

namespace App\Actions\Fortify;

use App\Models\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['phone'] = preg_replace('/\D/', '', $input['phone']);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'starts_with:998', 'size:12', 'unique:users'],
            'city' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable','string', 'max:255'],
            'group' => ['nullable','string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $details = [
            'city' => $input['city'],
            'school' => $input['school'],
            'group' => $input['group'],
        ];

        return User::create([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
            'details' => json_encode($details),
            'status' => UserStatus::MODERATION
        ]);
    }
}

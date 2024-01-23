<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    public function change()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail(__('Текущий пароль указан неверно.'));
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors(['error' => $validator->errors()->first()]);
        }

        auth()->user()->update(['password' => Hash::make($request->new_password)]);

        return redirect('/');
    }
}

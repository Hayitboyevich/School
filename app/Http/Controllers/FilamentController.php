<?php

namespace App\Http\Controllers;

class FilamentController extends Controller
{
    public function login()
    {
        return redirect()->route('login');
    }
}

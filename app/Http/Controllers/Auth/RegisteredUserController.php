<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required|in:male,female,other',
            'password' => 'required|string|confirmed|min:8',
        ]);

        Auth::login($user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'user_type' => 'user',
        ]));

        $user->assignRole('user');

        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }
}

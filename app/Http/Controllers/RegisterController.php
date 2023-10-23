<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:4',
                'max:16',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s/', $value)) {
                        $fail('Username cannot contain white spaces');
                    };
                },
                'unique:users'
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'dob' => ['required'],
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols()
            ],
            'password_confirmation' => ['required', 'min:8'],
            // 'locality' => ['required'],
            // 'personRole' => ['required'],
            // 'publicity' => ['required'],
            // 'terms' => ['required'],
        ]);


        $user = User::create([
            // 'salutation' => $request->get('salutation'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'dob' => $request->get('dob'),
            'password' => Hash::make($request->get('password')),
            // 'locality' => $request->get('locality'),
            // 'personRole' => $request->get('personRole'),
            // 'publicity' => $request->get('publicity'),
            // 'terms' => $request->get('terms'),
        ]);


        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(status: 201);
    }

    public function index()
    {
        // TODO: angeforderten Datensatz zurückgeben
        return response()->json(['message' => 'INDEX works from the Register Controller!'], Response::HTTP_OK);
    }
}

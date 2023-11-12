<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'locality' => ['required', Rule::in(['within_Germany', 'beyond_Germany'])],
            'personRole' => ['string', Rule::in(['author', 'child', 'librarian', 'opposed_to_the_biodiversity', 'publisher_representative', 'activist', 'binary_world_defender', 'journalist', 'curious_person'])],
            'publicity' => ['string', Rule::in(['other', 'mouthword', 'online_search'])],
            'terms' => ['required']
        ]);


        $user = User::create([
            'pronouns' => $request->get('pronouns'),
            'salutation' => $request->get('salutation'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'dob' => $request->get('dob'),
            'password' => Hash::make($request->get('password')),
            'locality' => $request->get('locality'),
            'personRole' => $request->get('personRole'),
            'publicity' => $request->get('publicity'),
            'terms' => $request->get('terms'),
        ]);


        Auth::login($user);
        $request->session()->regenerate();

        $username = $user->username;
        return response()->json(['message' => "Registration successful! You can now login, $username!"], Response::HTTP_CREATED);
    }

    public function index()
    {
        // TODO: angeforderten Datensatz zurückgeben
        return response()->json(['message' => 'Go ahead and register!'], Response::HTTP_OK);
    }
}

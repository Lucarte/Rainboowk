<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        // validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data',
                'errors' => $validator->errors(),
            ]);
        }

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $username = $user->username;

            return response()->json(['user' => $user, 'message' => "Login successful, $username!"], Response::HTTP_OK);
        } else {
            // Authentication failed
            return response()->json(['message' => 'Login failed'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function index()
    {
        // TODO: angeforderten Datensatz zurückgeben
        return response()->json(['message' => 'LOGIN PLEASE'], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            return response()->json(['user' => $user, 'message' => 'Login successful'], 200);
        } else {
            // Authentication failed
            return response()->json(['message' => 'Login failed'], 401);
        }
    }

    public function index()
    {
        // TODO: angeforderten Datensatz zurückgeben
        return response()->json(['message' => 'INDEX works from the Login Controller!'], Response::HTTP_OK);
    }
}

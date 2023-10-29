<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{

    public function getByUsername(Request $request, string $username)
    {
        // return $request->user('username');
        // return redirect(./user/{username})
        return response()->json(['message' => 'getByUsername from UserController WORKS!'], Response::HTTP_OK);
    }

    public function update(Request $request, string $username)
    {
        return response()->json(['message' => 'patch from UserController WORKS!'], Response::HTTP_OK);
    }

    public function delete(string $username)
    {
        return response()->json(['message' => 'delete from UserController WORKS!'], Response::HTTP_OK);
    }


    // Route for 'admin'

    public function users(Request $request)
    {
        return response()->json(['message' => 'userS from UserController WORKS!'], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{

    public function getByUsername(Request $request)
    {
        // return $request->user('Username');
        response()->json(['message' => 'getByUsername from UserController WORKS!'], Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        return response()->json(['message' => 'patch from UserController WORKS!'], Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        return response()->json(['message' => 'delete from UserController WORKS!'], Response::HTTP_OK);
    }
}

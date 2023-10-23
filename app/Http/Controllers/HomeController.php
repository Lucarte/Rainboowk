<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function getAll(Request $request)
    {
        // TODO: Alle Datensätze zurückgeben
        return response()->json(['message' => 'getAll from HomeController WORKS!'], Response::HTTP_OK);
    }
}

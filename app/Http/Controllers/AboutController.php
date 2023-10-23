<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AboutController extends Controller
{

    public function index(Request $request, string $book_lan)
    {
        return response()->json(['message' => 'show from AboutController WORKS!'], Response::HTTP_OK);
    }
}

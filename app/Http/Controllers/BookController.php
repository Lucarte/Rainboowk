<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function list(Request $request, string $book_lan)
    {
        return response()->json(['message' => 'list from BookController WORKS!'], Response::HTTP_OK);
    }

    public function show(Request $request, string $book_lan)
    {
        return response()->json(['message' => 'show from BookController WORKS!'], Response::HTTP_OK);
    }
}

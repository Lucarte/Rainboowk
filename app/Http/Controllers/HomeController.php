<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Buch;
use App\Models\Libro;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function getHome()
    {
        return response()->json(['message' => 'Welcome to the home API endpoint']);
    }

    public function getAll(Request $request)
    {
        $allBooks = [
            'books' => Book::all(),
            'libros' => Libro::all(),
            'livres' => Livre::all(),
            'buchs' => Buch::all(),
        ];

        return response()->json($allBooks, Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Libro;
use App\Models\Livre;
use App\Models\Buch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
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

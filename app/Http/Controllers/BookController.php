<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function create(Request $request)
    {
        try {
            $policyResp = Gate::inspect('create', Book::class);

            if ($policyResp->allowed()) {

                $rules = [
                    'ISBN' => 'required|isbn',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'author_id' => 'required|exists:authors,id', // check if still done this way or simply 'numeric' is enough
                    'illustrator_id' => 'nullable|exists:illustrators,id',
                    'print_date' => 'required|date', // Check if need to use releaseDate function like in Kids_Books project
                    'publisher_id' => 'required|exists:publishers,id',
                    'genre_id' => 'required|exists:genres,id',
                    'original_language' => 'required|string|max:255',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $book = new Book();
                $book->user_id;
                $book->ISBN = $request->input('ISBN');
                $book->title = $request->input('title');
                $book->description = $request->input('description');
                $book->author_id = $request->input('author_id');
                $book->illustrator_id = $request->input('illustrator_id');
                $book->print_date = $request->input('print_date');
                $book->publisher_id = $request->input('publisher_id');
                $book->genre_id = $request->input('genre_id');
                $book->original_language = $request->input('original_language');

                $book->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $book_lan, string $id)
    {
        try {
            $book = Book::find($book_lan, $id);

            $policyResp = Gate::inspect('delete', $book);

            if ($policyResp->allowed()) {
                $book->delete();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getById(string $book_lan, string $id)
    {
        try {
            $policyResp = Gate::inspect('getById', Book::class);

            if ($policyResp->allowed()) {
                $book = Book::find($book_lan, $id);

                return response()->json(['message' => $policyResp->message(), 'book' => $book], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function list(Request $request, string $book_lan)
    {
        try {
            $policyResp = Gate::inspect('list', Book::class);

            if ($policyResp->allowed()) {
                $books = Book::all($book_lan);

                return response()->json(['message' => $policyResp->message(), 'books' => $books], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $book_lan, string $id)
    {
        try {
            $book = Book::find($book_lan, $id);

            $policyResp = Gate::inspect('update', $book);

            if ($policyResp->allowed()) {
                $rules = [
                    'ISBN' => 'required|isbn',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'author_id' => 'required|exists:authors,id', // check if still done this way or simply 'numeric' is enough
                    'illustrator_id' => 'nullable|exists:illustrators,id',
                    'print_date' => 'required|date', // Check if need to use releaseDate function like in Kids_Books project
                    'publisher_id' => 'required|exists:publishers,id',
                    'genre_id' => 'required|exists:genres,id',
                    'original_language' => 'required|string|max:255',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $book->text = $request->input('title'); // update this line by necessity

                $book->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }



        $book = Book::find($id);
        $book->ISBN = $request->input('ISBN');

        $book->save();

        return response()->json(['message' => 'PATCH from BookController WORKS!'], Response::HTTP_OK);
    }
}

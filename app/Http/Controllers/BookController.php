<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Illustrator;
use App\Models\Publisher;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
                    'print_date' => 'required|date',
                    'original_language' => 'required|string|max:255',
                    'publisher_id' => 'required|exists:publishers,id', // New validation rule
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                // Check if a book with the same title and author exists
                $existingBook = Book::where('title', $request->input('title'))
                    ->where('author_id', $request->input('author_id'))
                    ->first();

                if ($existingBook) {
                    return response()->json(['message' => 'A book with the same title and author already exists.'], Response::HTTP_CONFLICT);
                }

                // Get the authenticated user
                $user = Auth::user();

                // Create a new book instance and set its attributes
                $book = new Book();
                $book->user_id = $user->id;
                $book->ISBN = $request->input('ISBN');
                $book->title = $request->input('title');
                $book->description = $request->input('description');
                $book->original_language = $request->input('original_language');

                // Find and set the publisher based on the provided 'publisher_id'
                $publisher = Publisher::find($request->input('publisher_id'));
                if (!$publisher) {
                    return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
                }

                $book->publisher()->associate($publisher);

                $book->print_date = $request->input('print_date');
                $book->author_id = $request->input('author_id');
                $book->illustrator_id = $request->input('illustrator_id');

                // Save the book
                $book->save();

                // Attach authors & illustrators
                if ($request->has('author_id')) {
                    $book->authors()->attach($request->input('author_id'));
                }
                if ($request->has('illustrator_id')) {
                    $book->illustrators()->attach($request->input('illustrator_id'));
                }

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function delete(string $title)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $book = Book::where('title', $title)->first();

            $policyResp = Gate::inspect('delete', $book);

            if ($policyResp->allowed()) {
                if ($book) {
                    $book->delete();
                    return response()->json(['message' => 'Book deleted successfully'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
                }
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function getByTitle(string $title)
    {
        try {
            $policyResp = Gate::inspect('getByTitle', Book::class);

            if ($policyResp->allowed()) {
                $book = Book::where('title', $title)->first();

                if ($book) {
                    return response()->json(['message' => $policyResp->message(), 'book' => $book], Response::HTTP_OK);
                }

                return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function list()
    {
        try {
            $policyResp = Gate::inspect('list', Book::class);

            if ($policyResp->allowed()) {
                $books = Book::all();

                return response()->json(['message' => $policyResp->message(), 'books' => $books], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update(Request $request, string $title)
    {
        try {
            $book = Book::where('title', $title)->first();

            $policyResp = Gate::inspect('update', $book);

            if ($policyResp->allowed()) {
                $rules = [
                    'ISBN' => 'required|isbn',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'print_date' => 'required|date',
                    'original_language' => 'required|string|max:255',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $book->title = $request->input('title');

                $book->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

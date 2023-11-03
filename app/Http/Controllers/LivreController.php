<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Publisher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class LivreController extends Controller
{
    public function create(Request $request)
    {
        try {
            $policyResp = Gate::inspect('create', Livre::class);

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

                // Check if a Livre with the same title and author exists
                $existingLivre = Livre::where('title', $request->input('title'))
                    ->where('author_id', $request->input('author_id'))
                    ->first();

                if ($existingLivre) {
                    return response()->json(['message' => 'A Livre with the same title and author already exists.'], Response::HTTP_CONFLICT);
                }

                // Get the authenticated user
                $user = Auth::user();

                // Create a new Livre instance and set its attributes
                $livre = new Livre();
                $livre->user_id = $user->id;
                $livre->ISBN = $request->input('ISBN');
                $livre->title = $request->input('title');
                $livre->description = $request->input('description');
                $livre->original_language = $request->input('original_language');

                // Find and set the publisher based on the provided 'publisher_id'
                $publisher = Publisher::find($request->input('publisher_id'));
                if (!$publisher) {
                    return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
                }

                $livre->publisher()->associate($publisher);

                $livre->print_date = $request->input('print_date');
                $livre->author_id = $request->input('author_id');
                $livre->illustrator_id = $request->input('illustrator_id');

                // Save the Livre
                $livre->save();

                // Attach authors & illustrators
                if ($request->has('author_id')) {
                    $livre->authors()->attach($request->input('author_id'));
                }
                if ($request->has('illustrator_id')) {
                    $livre->illustrators()->attach($request->input('illustrator_id'));
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

            $livre = Livre::where('title', $title)->first();

            $policyResp = Gate::inspect('delete', $livre);

            if ($policyResp->allowed()) {
                if ($livre) {
                    $livre->delete();
                    return response()->json(['message' => 'Livre deleted successfully'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'Livre not found'], Response::HTTP_NOT_FOUND);
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
            $livre = Livre::where('title', $title)->first();

            if ($livre) {
                return response()->json(['Livre' => $livre], Response::HTTP_NOT_FOUND);
            }

            // Book not found, return an error response
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function list()
    {
        try {
            $policyResp = Gate::inspect('list', Livre::class);

            if ($policyResp->allowed()) {
                $livres = Livre::all();

                return response()->json(['message' => $policyResp->message(), 'Livres' => $livres], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update(Request $request, string $title)
    {
        try {
            $livre = Livre::where('title', $title)->first();

            $policyResp = Gate::inspect('update', $livre);

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

                $livre->title = $request->input('title');

                $livre->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cover;
use App\Models\Libro;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class LibroController extends Controller
{
    public function create(Request $request, CoverController $coverController)
    {
        try {
            $policyResp = Gate::inspect('create', Libro::class);

            if ($policyResp->allowed()) {
                $rules = [
                    'ISBN' => 'required|isbn',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'print_date' => 'required|date',
                    'original_language' => 'required|string|max:255',
                    'publisher_id' => 'required|exists:publishers,id',
                    'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // New validation rule for the image
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                // Check if a Libro with the same title and author exists
                $existingLibro = Libro::where('title', $request->input('title'))
                    ->where('author_id', $request->input('author_id'))
                    ->first();

                if ($existingLibro) {
                    return response()->json(['message' => 'A Libro with the same title and author already exists.'], Response::HTTP_CONFLICT);
                }

                // Get the authenticated user
                $user = Auth::user();

                // Create a new Libro instance and set its attributes
                $libro = new Libro();
                $libro->user_id = $user->id;
                $libro->ISBN = $request->input('ISBN');
                $libro->title = $request->input('title');
                $libro->description = $request->input('description');
                $libro->original_language = $request->input('original_language');

                // Find and set the publisher based on the provided 'publisher_id'
                $publisher = Publisher::find($request->input('publisher_id'));
                if (!$publisher) {
                    return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
                }

                $libro->publisher()->associate($publisher);

                $libro->print_date = $request->input('print_date');
                $libro->author_id = $request->input('author_id');
                $libro->illustrator_id = $request->input('illustrator_id');

                // Save the Libro
                $libro->save();

                // Attach authors & illustrators
                if ($request->has('author_id')) {
                    $libro->authors()->attach($request->input('author_id'));
                }
                if ($request->has('illustrator_id')) {
                    $libro->illustrators()->attach($request->input('illustrator_id'));
                }

                // Handle image upload and storage
                if ($request->hasFile('image_path')) {
                    $extension = '.' . $request->file('image_path')->extension();
                    $title = $libro->title;
                    $path = $request->file('image_path')->storeAs(env('COVERS_UPLOAD'), time() . '_' . $title . $extension, 'public');

                    // Save cover information to the Covers table
                    $cover = new Cover();
                    $cover->user_id = $user->id;
                    $cover->libro_id = $libro->id; // Associate the cover with the newly created libro
                    $cover->image_path = $path;
                    $cover->save();
                }

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== '
                . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $title)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $libro = Libro::where('title', $title)->first();

            $policyResp = Gate::inspect('delete', $libro);

            if ($policyResp->allowed()) {
                if ($libro) {
                    $libro->delete();
                    return response()->json(['message' => 'Libro deleted successfully'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'Libro not found'], Response::HTTP_NOT_FOUND);
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
            $libro = Libro::where('title', $title)->first();

            if ($libro) {
                return response()->json(['libro' => $libro], Response::HTTP_OK);
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
        $libros = Libro::all();

        return response()->json(['message' => 'LISTA DE LIBROS', 'Libros' => $libros], Response::HTTP_OK);
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function update(Request $request, string $title)
    {
        try {
            $libro = Libro::where('title', $title)->first();

            $policyResp = Gate::inspect('update', $libro);

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

                $libro->title = $request->input('title');

                $libro->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

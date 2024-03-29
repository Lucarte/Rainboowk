<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Buch;
use App\Models\Cover;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class BuchController extends Controller
{
    public function create(Request $request, CoverController $coverController)
    {
        try {
            $policyResp = Gate::inspect('create', Buch::class);

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

                // Check if a Buch with the same title and author exists
                $existingBuch = Buch::where('title', $request->input('title'))
                    ->where('author_id', $request->input('author_id'))
                    ->first();

                if ($existingBuch) {
                    return response()->json(['message' => 'A Buch with the same title and author already exists.'], Response::HTTP_CONFLICT);
                }

                // Get the authenticated user
                $user = Auth::user();

                // Create a new Buch instance and set its attributes
                $buch = new Buch();
                $buch->user_id = $user->id;
                $buch->ISBN = $request->input('ISBN');
                $buch->title = $request->input('title');
                $buch->description = $request->input('description');
                $buch->original_language = $request->input('original_language');

                // Find and set the publisher based on the provided 'publisher_id'
                $publisher = Publisher::find($request->input('publisher_id'));
                if (!$publisher) {
                    return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
                }

                $buch->publisher()->associate($publisher);

                $buch->print_date = $request->input('print_date');
                $buch->author_id = $request->input('author_id');
                $buch->illustrator_id = $request->input('illustrator_id');

                // Save the Buch
                $buch->save();

                // Attach authors & illustrators
                if ($request->has('author_id')) {
                    $buch->authors()->attach($request->input('author_id'));
                }
                if ($request->has('illustrator_id')) {
                    $buch->illustrators()->attach($request->input('illustrator_id'));
                }

                // Handle image upload and storage
                if ($request->hasFile('image_path')) {
                    $extension = '.' . $request->file('image_path')->extension();
                    $title = $buch->title;
                    $path = $request->file('image_path')->storeAs(env('COVERS_UPLOAD'), time() . '_' . $title . $extension, 'public');

                    // Save cover information to the Covers table
                    $cover = new Cover();
                    $cover->user_id = $user->id;
                    $cover->buch_id = $buch->id; // Associate the cover with the newly created buch
                    $cover->image_path = $path;
                    $cover->save();
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

            $buch = Buch::where('title', $title)->first();

            $policyResp = Gate::inspect('delete', $buch);

            if ($policyResp->allowed()) {
                if ($buch) {
                    $buch->delete();
                    return response()->json(['message' => 'Buch deleted successfully'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'Buch not found'], Response::HTTP_NOT_FOUND);
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
            $buch = Buch::where('title', $title)->first();

            if ($buch) {
                return response()->json(['Buch' => $buch], Response::HTTP_OK);
            }

            // Buch not found, return an error response
            return response()->json(['message' => 'Buch not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

public function list()
{
    try {
        $buecher = Buch::all();

        return response()->json(['message' => 'BUECHERLIST', 'Buecher' => $buecher], Response::HTTP_OK);
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function update(Request $request, string $title)
    {
        try {
            $buch = Buch::where('title', $title)->first();

            $policyResp = Gate::inspect('update', $buch);

            if ($policyResp->allowed()) {
                $rules = [
                    'ISBN' => 'required|isbn',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'print_date' => 'required|date',
                    'original_language' => 'required|string|max:255'
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $buch->ISBN = $request->input('ISBN');
                $buch->title = $request->input('title');
                $buch->description = $request->input('description');
                $buch->original_language = $request->input('original_language');

                $buch->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

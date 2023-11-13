<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Buch;
use App\Models\Cover;
use App\Models\Libro;
use App\Models\Livre;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CoverController extends Controller
{
    public function uploadCover(Request $request)
    {
        try {

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('uploadCover', Cover::class);

            if ($policyResp->allowed()) {
                // Validate the incoming data
                $validatedData = $request->validate([
                    'user_id' => 'required|exists:users,id', // Validate the user_id
                    'book_id' => 'required_without_all:libro_id,livre_id,buch_id|integer',
                    'libro_id' => 'required_without_all:book_id,livre_id,buch_id|integer',
                    'livre_id' => 'required_without_all:book_id,libro_id,buch_id|integer',
                    'buch_id' => 'required_without_all:book_id,libro_id,livre_id|integer',
                    'image_path' => 'required|image|mimes:jpeg,png,gif',
                ]);

                // Create a new cover
                $cover = new Cover([
                    'image_path' => $validatedData['image_path'],
                    'user_id' => $validatedData['user_id']
                ]);

                if ($request->has('book_id')) {
                    $book = Book::findOrFail($request->input('book_id'));
                    $book->cover()->save($cover);
                } elseif ($request->has('libro_id')) {
                    $libro = Libro::findOrFail($request->input('libro_id'));
                    $libro->cover()->save($cover);
                } elseif ($request->has('livre_id')) {
                    $livre = Livre::findOrFail($request->input('livre_id'));
                    $livre->cover()->save($cover);
                } elseif ($request->has('buch_id')) {
                    $buch = Buch::findOrFail($request->input('buch_id'));
                    $buch->cover()->save($cover);
                }

                // Save the cover
                $cover->save();

                // Handle image upload and storage
                if (!$request->hasFile('image_path')) {
                    return response()->json(['message' => 'No cover saved.'], Response::HTTP_BAD_REQUEST);
                }

                // Set the file path in the storage system using the time, title related to it, and file extension
                $extension = '.' . $request->file('image_path')->extension();
                $title = $book->title ?? $libro->title ?? $livre->title ?? $buch->title;
                $path = $request->file('image_path')->storeAs(env('COVERS_UPLOAD'), time() . '_' . $title . $extension, 'public');

                // Set the image_path in the database to the path where it's stored
                $cover->image_path = $path;
                $cover->save();

                return response()->json(['message' => 'Cover created successfully under ' . $path], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCover(int $id)
    {
        try {
            // Find the Cover by ID
            // $cover = Cover::where('id', $id)->first();
            $cover = Cover::find($id);

            if (!$cover) {
                return response()->json(['message' => 'Cover not found'], Response::HTTP_NOT_FOUND);
            }

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('deleteCover', $cover);

            if ($policyResp->allowed()) {
                if ($cover) {
                    // Delete the cover
                    $cover->delete();
                    return response()->json(['message' => 'Cover deleted successfully'], Response::HTTP_OK);
                }
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function updateCover(Request $request, $id)
    {
        try {
            // Find the Cover by ID
            $cover = Cover::findOrFail($id);

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('updateCover', $cover);

            if ($policyResp->allowed()) {
                $validatedData = $request->validate([
                    // 'user_id' => 'required|exists:users,id',
                    'image_path' => 'nullable|image|mimes:jpeg,png,gif',
                ]);

                // Check if image_path has changed and a valid file is present
                if ($request->hasFile('image_path')) {

                    // Update COver
                    $cover->update([
                        // 'user_id' => $validatedData['user_id'],
                        'image_path' => $request->input('image_path')
                    ]);

                    // Handle image upload and storage
                    $extension = '.' . $request->file('image_path')->extension();
                    $title = $cover->book->title ?? $cover->libro->title ?? $cover->livre->title ?? $cover->buch->title;
                    $path = $request->file('image_path')->storeAs(env('COVERS_UPLOAD'), time() . '_' . $title . $extension, 'public');

                    // Set cover path
                    $cover->image_path = $path;
                }

                // Save changes
                $cover->save();

                return response()->json(['message' => 'Cover updated successfully'], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

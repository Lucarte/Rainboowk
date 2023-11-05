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
            // Authenticate the user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('uploadCover', Cover::class);

            if ($policyResp->allowed()) {
                // Validate the incoming data
                $validatedData = $request->validate([
                    'book_id' => 'required_without_all:libro_id,livre_id,buch_id|integer',
                    'libro_id' => 'required_without_all:book_id,livre_id,buch_id|integer',
                    'livre_id' => 'required_without_all:book_id,libro_id,buch_id|integer',
                    'buch_id' => 'required_without_all:book_id,libro_id,livre_id|integer',
                    'image_path' => 'required|image|mimes:jpeg,png,gif',
                ]);

                // Create a new cover
                $cover = new Cover([
                    'image_path' => $validatedData['image_path']
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

                // Save the cover and associate it with the appropriate model
                $cover->save();

                // Handle image upload and storage
                if (!$request->hasFile('image_path')) {
                    return response()->json(['message' => 'No cover saved'], Response::HTTP_BAD_REQUEST);
                }

                $extension = '.' . $request->file('image_path')->extension();
                $path = $request->file('image_path')->storeAs('covers', time() . '_' . $user->id . '_' . 'cover' . $extension, 'public');

                return response()->json(['message' => 'Cover created successfully under ' . $path], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function deleteCover(Cover $cover)
    {
        try {
            // Authenticate the user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('deleteCover', $cover);

            if ($policyResp->allowed()) {
                // Dissociate the cover association from the book, libro, livre, or buch (if applicable)
                if ($cover->bookCovers) {
                    $cover->bookCovers->book->cover()->dissociate();
                    $cover->bookCovers->delete();
                } elseif ($cover->libroCovers) {
                    $cover->libroCovers->libro->cover()->dissociate();
                    $cover->libroCovers->delete();
                } elseif ($cover->livreCovers) {
                    $cover->livreCovers->livre->cover()->dissociate();
                    $cover->livreCovers->delete();
                } elseif ($cover->buchCovers) {
                    $cover->buchCovers->buch->cover()->dissociate();
                    $cover->buchCovers->delete();
                }

                // Delete the cover and associated image file (if applicable)
                $cover->delete();

                return response()->json(['message' => 'Cover deleted successfully'], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function editCover(Request $request, Cover $cover)
    {
        try {
            // Authenticate the user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('editCover', $cover);

            if ($policyResp->allowed()) {
                // Validate and update cover information based on your application's requirements

                $cover->update([
                    'user_id' => $request->input('user_id'),
                    'image_path' => $request->input('image_path'),
                    // Update other fields as needed
                ]);

                // Handle image update and storage here

                return response()->json(['message' => 'Cover updated successfully'], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // public function updateCover(Request $request, Cover $cover)
    // {
    //     try {
    //         // Authenticate the user
    //         $user = Auth::user();

    //         if (!$user) {
    //             return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    //         }

    //         // Check authorization using Gate policy
    //         $policyResp = Gate::inspect('updateCover', $cover);

    //         if ($policyResp->allowed()) {
    //             // Determine which ID has been provided in the request
    //             $ids = ['book_id', 'libro_id', 'livre_id', 'buch_id'];
    //             $idProvided = null;

    //             foreach ($ids as $id) {
    //                 if ($request->has($id)) {
    //                     $idProvided = $id;
    //                     break;
    //                 }
    //             }

    //             if (!$idProvided) {
    //                 return response()->json(['message' => 'No valid ID provided'], Response::HTTP_BAD_REQUEST);
    //             }

    //             // Update the cover based on the provided ID
    //             if ($idProvided === 'book_id') {
    //                 $book = Book::findOrFail($request->input('book_id'));
    //                 $book->cover()->associate($cover);
    //                 $book->save();
    //             } elseif ($idProvided === 'libro_id') {
    //                 $libro = Libro::findOrFail($request->input('libro_id'));
    //                 $libro->cover()->associate($cover);
    //                 $libro->save();
    //             } elseif ($idProvided === 'livre_id') {
    //                 $livre = Livre::findOrFail($request->input('livre_id'));
    //                 $livre->cover()->associate($cover);
    //                 $livre->save();
    //             } elseif ($idProvided === 'buch_id') {
    //                 $buch = Buch::findOrFail($request->input('buch_id'));
    //                 $buch->cover()->associate($cover);
    //                 $buch->save();
    //             }

    //             // Update other fields if needed
    //             $cover->update([
    //                 'user_id' => $request->input('user_id'),
    //                 'image_path' => $request->input('image_path'),
    //                 // Update other fields as needed
    //             ]);

    //             // Handle image update and storage here

    //             return response()->json(['message' => 'Cover updated successfully'], Response::HTTP_OK);
    //         }

    //         return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
    //     } catch (Exception $e) {
    //         return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }



}

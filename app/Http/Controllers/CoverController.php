<?php

namespace App\Http\Controllers;

use App\Models\Cover;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CoverController extends Controller
{
    public function deleteCover(int $id)
    {
        try {
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
        $validatedData = $request->validate([
            'image_path' => 'image|mimes:jpeg,png,gif',
        ]);
        try {
            // Find the Cover by ID
            $cover = Cover::findOrFail($id);

            // Check authorization using Gate policy
            $policyResp = Gate::inspect('updateCover', $cover);

            if ($policyResp->allowed()) {
                // Check if image_path has changed and a valid file is present
                if ($request->hasFile('image_path')) {

                    // Handle image upload and storage
                    $extension = '.' . $request->file('image_path')->extension();
                    $title = $cover->book->title ?? $cover->libro->title ?? $cover->livre->title ?? $cover->buch->title;
                    $path = $request->file('image_path')->storeAs(env('COVERS_UPLOAD'), time() . '_' . $title . $extension, 'public');
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

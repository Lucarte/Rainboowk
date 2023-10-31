<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{

    public function createAuthor(Request $request)
    {
        try {
            $policyResp = Gate::inspect('createAuthor', Author::class);

            if ($policyResp->allowed()) {

                // Validate author data
                $rules = [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'date_of_birth' => 'date|nullable',
                    'date_of_death' => 'date|nullable',
                    'biography' => 'nullable',
                    'nationality' => 'max:255|nullable',
                    'contact_email' => 'email|max:255|nullable|unique:authors',
                    'website' => 'max:255|nullable',
                    'awards_and_honors' => 'nullable'
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $user = Auth::user(); // Get the authenticated user

                // Store Author data
                $author = new Author();
                $author->user_id = $user->id; // Set the 'user_id' based on the authenticated user
                $author->first_name = $request->input('first_name');
                $author->last_name = $request->input('last_name');
                $author->date_of_birth = $request->input('date_of_birth');
                $author->date_of_death = $request->input('date_of_death');
                $author->biography = $request->input('biography');
                $author->nationality = $request->input('nationality');
                $author->contact_email = $request->input('contact_email');
                $author->website = $request->input('website');
                $author->awards_and_honors = $request->input('awards_and_honor');

                $author->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAuthor($slug)
    {
        // Convert the URL parameter with underscores to match the format in the 'fullname' column
        $formattedSlug = str_replace('_', ' ', $slug);

        // Find the author using the formatted slug
        $author = Author::where('fullname', $formattedSlug)->first();

        if ($author) {
            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('deleteAuthor', $author);

            if ($policyResp->allowed()) {
                // Delete the author
                $author->delete();

                return response()->json(['message' => 'Author deleted successfully'], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        }

        return response()->json(['message' => 'Author not found'], Response::HTTP_NOT_FOUND);
    }



    // public function deleteAuthor($slug)
    // {
    //     $author = Author::where('fullname_slug', $slug)->first();

    //     if ($author) {
    //         // Delete the author
    //         $author->delete();

    //         return response()->json(['message' => 'Author deleted successfully'], 200);
    //     }

    //     return response()->json(['message' => 'Author not found'], 404);
    // }

    public function getByFullname($slug)
    {
        $author = Author::where('fullname', $slug)->first();

        if ($author) {
            // Author found, retrieve the author's books
            $books = $author->books;

            // Return the author's information and their books
            return response()->json(['author' => $author, 'books' => $books]);
        }

        // Author not found, return an error response
        return response()->json(['message' => 'Author not found'], Response::HTTP_NOT_FOUND);
    }

    public function updateAuthor(Request $request, $slug)
    {
        $author = Author::where('fullname', $slug)->first();

        if ($author) {
            // Update the author's information based on the request data
            $author->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                // Add other fields you want to update
            ]);

            return response()->json(['message' => 'Author updated successfully'], 200);
        }

        return response()->json(['message' => 'Author not found'], 404);
    }
}

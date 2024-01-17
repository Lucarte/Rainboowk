<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Rules\UniqueAuthorNameRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{

    public function checkAuthorExistence(Request $request)
{
    $firstName = $request->input('first_name');
    $lastName = $request->input('last_name');

    // Check if the author with the given first and last names exists in the database
    $author = Author::where('first_name', $firstName)
        ->where('last_name', $lastName)
        ->first();

    }
    
public function checkAuthor(Request $request)
{
    try {
        // Validate request data
        $rules = [
            'first_name' => 'required|max:255|alpha',
            'last_name' => 'required|max:255|alpha',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        
        // Check if an author with the same first name and last name already exists
        $existingAuthor = Author::where('first_name', $request->input('first_name'))
            ->where('last_name', $request->input('last_name'))
            ->first();
        
        $exists = $existingAuthor ? true : false;
        $authorId = $exists ? $existingAuthor->id : null;

        if ($existingAuthor) {
            return response()->json(['exists' => $exists, 'authorId' => $authorId]);
        } else {
            return response()->json(['message' => 'No author found'], Response::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



public function list()
{
    try {
        $authors = Author::all();

        return response()->json(['message' => 'AUTHORS LIST', 'Authors' => $authors], Response::HTTP_OK);
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function createAuthor(Request $request)
    {
        try {
            $policyResp = Gate::inspect('createAuthor', Author::class);

            if ($policyResp->allowed()) {

                // Validate author data
                $rules = [
                    'first_name' => 'required|max:255|alpha',
                    'last_name' => 'required|max:255|alpha',
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

                // Check if an author with the same first name and last name already exists
                $existingAuthor = Author::where('first_name', $request->input('first_name'))
                    ->where('last_name', $request->input('last_name'))
                    ->first();

                if ($existingAuthor) {
                    return response()->json(['message' => 'This author already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Retrieve user
                $user = Auth::user();

                if (!$user) {
                    return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
                }

                // Store Author data
                $author = new Author();
                $author->user_id = $user->id; // Set the 'user_id' based on the user found by username
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
        try {

            // Retrieve user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Convert the URL parameter with underscores to match the format in the 'fullname' column
            $formattedSlug = str_replace('_', ' ', $slug);

            // Find the author using the formatted slug
            $author = Author::where('fullname', $formattedSlug)->firstOrFail();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('deleteAuthor', $author);

            if ($policyResp->allowed()) {
                $author->delete();
                return response()->json(['message' => 'Author deleted successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Author not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function getByFullname($slug)
    {
        try {
            // Find the author using the provided slug
            $author = Author::where('fullname', str_replace('_', ' ', $slug))->first();

            if ($author) {
                // Author found, retrieve the author's books
                $books = $author->books;

                // Return the author's information and their books
                return response()->json(['author' => $author, 'books' => $books], Response::HTTP_OK);
            }

            // Author not found, return an error response
            return response()->json(['message' => 'Author not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAuthor(Request $request, $slug)
    {
        try {
            // Retrieve user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            // Convert the URL parameter with underscores to match the format in the 'fullname' column
            $formattedSlug = str_replace('_', ' ', $slug);
                    

            // Find the author using the formatted slug
            $author = Author::where('fullname', $formattedSlug)->firstOrFail();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('updateAuthor', $author);

            if ($policyResp->allowed()) {

                // Validate author data
                $rules = [
                    'first_name' => 'required|max:255|alpha',
                    'last_name' => 'required|max:255|alpha',
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

                // Check if an author with the same first name and last name already exists
                $existingAuthor = Author::where('first_name', $request->input('first_name'))
                    ->where('last_name', $request->input('last_name'))
                    ->first();

                if ($existingAuthor) {
                    return response()->json(['message' => 'This author already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Update the author's information based on the request data
                $author->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'date_of_death' => $request->input('date_of_death'),
                    'biography' => $request->input('biography'),
                    'nationality' => $request->input('nationality'),
                    'contact_email' => $request->input('contact_email'),
                    'website' => $request->input('website'),
                    'awards_and_honors' => $request->input('awards_and_honor')
                ]);

                return response()->json(['message' => 'Author updated successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Author not found'], Response::HTTP_NOT_FOUND);
        }
    }
}

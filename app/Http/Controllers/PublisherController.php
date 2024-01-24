<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{

        public function checkPublisherExistence(Request $request)
{
    $firstName = $request->input('name');

    // Check if the publisher with the given first and last names exists in the database
    $publisher = Publisher::where('name', $firstName)
        ->first();

    }
    
public function checkPublisher(Request $request)
{
    try {
        // Validate request data
        $rules = [
            'name' => 'required|max:255|alpha'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        
        // Check if an publisher with the same first name and last name already exists
        $existingPublisher = Publisher::where('name', $request->input('name'))
            ->first();
        
        $exists = $existingPublisher ? true : false;
        $publisherId = $exists ? $existingPublisher->id : null;

        if ($existingPublisher) {
            return response()->json(['exists' => $exists, 'publisherId' => $publisherId]);
        } else {
            return response()->json(['message' => 'No publisher found'], Response::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function list()
{
    try {
        $publishers = Publisher::all();

        return response()->json(['message' => 'PUBLISHERS LIST', 'Publishers' => $publishers], Response::HTTP_OK);
    } catch (Exception $e) {
        return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    public function createPublisher(Request $request)
    {
        try {
            $policyResp = Gate::inspect('createPublisher', Publisher::class);

            if ($policyResp->allowed()) {

                // Validate Publisher data
                $rules = [
                    'name' => 'required|max:255',
                    'description' => 'nullable',
                    'website' => 'max:255|nullable',
                    'foundation_year' => 'date|nullable'
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                // Check if a publisher with the same first name and last name already exists
                $existingPublisher = Publisher::where('name', $request->input('name'))->first();

                if ($existingPublisher) {
                    return response()->json(['message' => 'This publisher already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Find the user 
                $user = Auth::user();

                if (!$user) {
                    return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
                }

                // Store Publisher data
                $publisher = new Publisher();
                $publisher->user_id = $user->id; // Set the 'user_id' based on the user found by username
                $publisher->name = $request->input('name');
                $publisher->description = $request->input('description');
                $publisher->website = $request->input('website');
                $publisher->foundation_year = $request->input('foundation_year');

                $publisher->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deletePublisher($name)
    {
        try {

            // Retrieve user 
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Find the Publisher using its name
            $publisher = Publisher::where('name', $name)->first();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('deletePublisher', $publisher);

            if ($policyResp->allowed()) {
                $publisher->delete();
                return response()->json(['message' => 'Publisher deleted successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function getByName($name)
    {
        try {
            // Find the Publisher using the provided name
            $publisher = Publisher::where('name', $name)->first();

            if ($publisher) {
                // Publisher found, retrieve the Publisher's books
                $books = $publisher->books;

                // Return the Publisher's information and their books
                return response()->json(['Publisher' => $publisher, 'books' => $books], Response::HTTP_OK);
            }

            // Publisher not found, return an error response
            return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // WORKING FOR CHECKING IF ALREADY EXISTS, from URL
    public function updatePublisher(Request $request, $name)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            // // Retrieve the user based on the provided username
            // $user = User::where('username', $username)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Find the Publisher using its name
            $publisher = Publisher::where('name', $name)->firstOrFail();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('updatePublisher', $publisher);

            if ($policyResp->allowed()) {

                // Validate Publisher data
                $rules = [
                    'name' => 'required|max:255',
                    'description' => 'nullable',
                    'website' => 'max:255|nullable',
                    'foundation_year' => 'date|nullable'
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                // Check if a publisher with the same name and website already exists
                $existingPublisher = Publisher::where('name', $request->input('name'))
                    ->first();

                if ($existingPublisher) {
                    return response()->json(['message' => 'This publisher already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Update the Publisher's information based on the request data
                $publisher->update([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'website' => $request->input('website'),
                    'foundation_year' => $request->input('foundation_year')
                ]);

                return response()->json(['message' => 'Publisher updated successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
        }
    }
}

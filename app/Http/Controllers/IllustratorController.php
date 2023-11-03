<?php

namespace App\Http\Controllers;

use App\Models\Illustrator;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class IllustratorController extends Controller
{

    public function createIllustrator(Request $request)
    {
        try {
            $policyResp = Gate::inspect('createIllustrator', Illustrator::class);

            if ($policyResp->allowed()) {

                // Validate Illustrator data
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

                // Check if an illustrator with the same first name and last name already exists
                $existingIllustrator = Illustrator::where('first_name', $request->input('first_name'))
                    ->where('last_name', $request->input('last_name'))
                    ->first();

                if ($existingIllustrator) {
                    return response()->json(['message' => 'This illustrator already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Find user
                $user = Auth::user();

                if (!$user) {
                    return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
                }

                // Store Illustrator data
                $illustrator = new Illustrator();
                $illustrator->user_id = $user->id; // Set the 'user_id' based on the user found by username
                $illustrator->first_name = $request->input('first_name');
                $illustrator->last_name = $request->input('last_name');
                $illustrator->date_of_birth = $request->input('date_of_birth');
                $illustrator->date_of_death = $request->input('date_of_death');
                $illustrator->biography = $request->input('biography');
                $illustrator->nationality = $request->input('nationality');
                $illustrator->contact_email = $request->input('contact_email');
                $illustrator->website = $request->input('website');
                $illustrator->awards_and_honors = $request->input('awards_and_honor');

                $illustrator->save();

                return response()->json(['message' => $policyResp->message()], Response::HTTP_CREATED);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteIllustrator($slug)
    {
        try {

            // Retrieve user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Convert the URL parameter with underscores to match the format in the 'fullname' column
            $formattedSlug = str_replace('_', ' ', $slug);

            // Find the Illustrator using the formatted slug
            $illustrator = Illustrator::where('fullname', $formattedSlug)->firstOrFail();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('deleteIllustrator', $illustrator);

            if ($policyResp->allowed()) {
                $illustrator->delete();
                return response()->json(['message' => 'Illustrator deleted successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Illustrator not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function getByFullname($slug)
    {
        try {
            // Find the Illustrator using the provided slug
            $illustrator = Illustrator::where('fullname', str_replace('_', ' ', $slug))->first();

            if ($illustrator) {
                // Illustrator found, retrieve the Illustrator's books
                $books = $illustrator->books;

                // Return the Illustrator's information and their books
                return response()->json(['Illustrator' => $illustrator, 'books' => $books], Response::HTTP_OK);
            }

            // Illustrator not found, return an error response
            return response()->json(['message' => 'Illustrator not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateIllustrator(Request $request, $slug)
    {
        try {
            // Retrieve user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Convert the URL parameter with underscores to match the format in the 'fullname' column
            $formattedSlug = str_replace('_', ' ', $slug);

            // Find the Illustrator using the formatted slug
            $illustrator = Illustrator::where('fullname', $formattedSlug)->firstOrFail();

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('updateIllustrator', $illustrator);

            if ($policyResp->allowed()) {

                // Validate Illustrator data
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

                // Check if an illustrator with the same first name and last name already exists
                $existingIllustrator = Illustrator::where('first_name', $request->input('first_name'))
                    ->where('last_name', $request->input('last_name'))
                    ->first();

                if ($existingIllustrator) {
                    return response()->json(['message' => 'This illustrator already exists in the database.'], Response::HTTP_CONFLICT);
                }

                // Update the Illustrator's information based on the request data
                $illustrator->update([
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

                return response()->json(['message' => 'Illustrator updated successfully'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Illustrator not found'], Response::HTTP_NOT_FOUND);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function getByUsername(Request $request, string $username)
    {
        try {
            // // Find the User using their username
            $user = User::where('username', $username)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Check if the current user has the necessary permission
            $policyResp = Gate::inspect('getByUsername', $user);

            if ($policyResp->allowed()) {
                return response()->json(['user' => $user], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $username)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
            }
            $user = User::where('username', $username)->first();

            $policyResp = Gate::inspect('delete', $user);

            if ($policyResp->allowed()) {
                if ($user) {
                    $user->delete();
                    return response()->json(['message' => 'User deleted successfully'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
                }
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $username)
    {
        try {
            $user = User::where('username', $username)->first();

            $policyResp = Gate::inspect('update', $user);

            if ($policyResp->allowed()) {
                $rules = [
                    'username' => [
                        'required',
                        'string',
                        'min:4',
                        'max:16',
                        function ($attribute, $value, $fail) {
                            if (preg_match('/\s/', $value)) {
                                $fail('Username cannot contain white spaces');
                            };
                        },
                        'unique:users'
                    ],
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore($user->id)
                    ],
                    'dob' => ['required'],
                    'password' => [
                        'required', 'string', 'confirmed',
                        Password::min(8)->letters()->numbers()->mixedCase()->symbols()
                    ],
                    'password_confirmation' => ['required', 'min:8'],
                    'locality' => ['required', Rule::in(['within_Germany', 'beyond_Germany'])],
                    'personRole' => ['string', Rule::in(['author', 'child', 'librarian', 'opposed_to_the_biodiversity', 'publisher_representative', 'activist', 'binary_world_defender', 'journalist', 'curious_person'])],
                    'publicity' => ['string', Rule::in(['other', 'mouthword', 'online_search'])],
                    'terms' => ['required'],
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], Response::HTTP_BAD_REQUEST);
                }

                $user->username = $request->input('username');
                $user->email = $request->input('email');
                $user->dob = $request->input('dob');
                $user->password = bcrypt($request->input('password'));
                $user->locality = $request->input('locality');
                $user->personRole = $request->input('personRole');
                $user->publicity = $request->input('publicity');
                $user->terms = $request->input('terms');

                $user->save();

                return response()->json(['user' => $user], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // Route for 'admin'
    public function usersList()
    {
        try {
            $policyResp = Gate::inspect('usersList', User::class);

            if ($policyResp->allowed()) {
                // Retrieve the list of users
                $users = User::all();

                return response()->json(['message' => $policyResp->message(), 'users' => $users], Response::HTTP_OK);
            }

            return response()->json(['message' => $policyResp->message()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

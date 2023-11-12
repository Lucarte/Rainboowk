<?php

namespace App\Policies;

use App\Http\Controllers\UserController;
use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuthorPolicy
{
    public function createAuthor(User $user)
    {
        return $user !== null ? Response::allow('AuthorPolicy - createAuthor - allowed') : Response::deny('You do not have permission to create an author.');
    }

    public function deleteAuthor(User $user, Author $author)
    {
        // Check if the user is an admin or the owner of the author
        if ($user->isAdmin() || $user->id === $author->user_id) {
            return Response::allow('AuthorPolicy - deleteAuthor - allowed');
        }

        return Response::deny('You do not have permission to delete this author.');
    }

    public function getByFullname(User $user, Author $author)
    {
        if (!$author) {
            // Author not found, return "not found" (HTTP 404) response.
            return response()->json(['message' => 'Author not found'], 404);
        }

        // Check if the user is an admin or the owner of the author
        if ($user->isAdmin() || $user->id === $author->user_id) {
            return Response::allow('AuthorPolicy - getByFullname - allowed');
        }

        // User is neither an admin nor the owner, return "deny."
        return Response::deny('AuthorPolicy - getByFullname - denied');
    }

    public function updateAuthor(User $user, Author $author)
    {
        // Check if the user is an admin or the owner of the author
        if ($user->isAdmin() || $user->id === $author->user_id) {
            return Response::allow('AuthorPolicy - deleteAuthor - allowed');
        }
        return Response::deny('AuthorPolicy - updateAuthor - denied');
    }
}

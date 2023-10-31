<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuthorPolicy
{
    // Do not need this for  the author 
    // // true null vs. true false // change this to a real check od admin 'role'
    // public function before(User $user)
    // {
    //     return LoginController::isAdmin($user) ? true : null;
    // }


    public function createAuthor(User $user)
    {
        return $user !== null ? Response::allow('AuthorPolicy - createAuthor - allowed') : Response::deny('You do not have permission to create an author.');
    }

    public function deleteAuthor(User $user, Author $author)
    {
        // Check if the user is an admin or the owner of the author
        if (LoginController::isAdmin($user) || $user->id === $author->user_id) {
            return Response::allow('AuthorPolicy - deleteAuthor - allowed');
        }

        return Response::deny('You do not have permission to delete this author.');
    }



    // public function deleteAuthor(User $user, Author $book)
    // {
    //     return $user->user_id === $author->user_id ? Response::allow('AuthorPolicy - deleteAuthor - allowed') : Response::deny('AuthorPolicy - deleteAuthor - denied');
    // }

    // // Nur für 'admin'
    // public function getAll(User $user, Author $book)
    // {
    //     return $user->username === $book->user_id ? Response::allow('AuthorPolicy - getAll - allowed') : Response::deny('AuthorPolicy - getAll - denied');
    // }

    // public function getById(User $user, Author $book)
    // {
    //     // Is someone logged in...
    //     return $user->username !== null ? Response::allow('AuthorPolicy - getById - allowed') : Response::deny('AuthorPolicy - getById - denied');
    // }

    // public function updateAuthor(User $user, Author $book)
    // {
    //     return $user->username === $book->user_id ? Response::allow('AuthorPolicy - updateAuthor - allowed') : Response::deny('AuthorPolicy - updateAuthor - denied');
    // }
}

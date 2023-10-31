<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\User;
use App\Models\Book;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    // true null vs. true false // change this to a real check od admin 'role'
    public function before(User $user)
    {
        return LoginController::isAdmin($user) ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('BookPolicy - create - allowed') : Response::deny('BookPolicy - create - denied');
    }

    public function delete(User $user, Book $book)
    {
        return $user->username === $book->user_id ? Response::allow('BookPolicy - delete - allowed') : Response::deny('BookPolicy - delete - denied');
    }

    // Nur für 'admin'
    public function getAll(User $user, Book $book)
    {
        return $user->username === $book->user_id ? Response::allow('BookPolicy - getAll - allowed') : Response::deny('BookPolicy - getAll - denied');
    }

    public function getById(User $user, Book $book)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('BookPolicy - getById - allowed') : Response::deny('BookPolicy - getById - denied');
    }

    public function update(User $user, Book $book)
    {
        return $user->username === $book->user_id ? Response::allow('BookPolicy - update - allowed') : Response::deny('BookPolicy - update - denied');
    }
}

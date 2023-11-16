<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    // true null vs. true false
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('BookPolicy - create - allowed') : Response::deny('BookPolicy - create - denied');
    }

    public function delete(User $user, Book $book)
    {
        return $user->id === $book->user_id ? Response::allow('BookPolicy - delete - allowed') : Response::deny('BookPolicy - delete - denied');
    }

    public function list()
    {
        return Response::allow('BookPolicy - list - allowed');
    }

    public function getByTitle(User $user)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('BookPolicy - getByTitle - allowed') : Response::deny('BookPolicy - getByTitle - denied');
    }

    public function update(User $user, Book $book)
    {
        return $user->id === $book->user_id ? Response::allow('BookPolicy - update - allowed') : Response::deny('BookPolicy - update - denied');
    }
}

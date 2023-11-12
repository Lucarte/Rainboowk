<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\User;
use App\Models\Libro;
use Illuminate\Auth\Access\Response;

class LibroPolicy
{
    // true null vs. true false
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('LibroPolicy - create - allowed') : Response::deny('LibroPolicy - create - denied');
    }

    public function delete(User $user, Libro $libro)
    {
        return $user->id === $libro->user_id ? Response::allow('LibroPolicy - delete - allowed') : Response::deny('LibroPolicy - delete - denied');
    }

    public function list()
    {
        return Response::allow('LibroPolicy - list - allowed');
    }

    public function getByTitle(Libro $libro)
    {
        // Is someone logged in...
        return $libro->title !== null ? Response::allow('LibroPolicy - getByTitle - allowed') : Response::deny('LibroPolicy - getByTitle - denied');
    }

    public function update(User $user, Libro $libro)
    {
        return $user->id === $libro->user_id ? Response::allow('LibroPolicy - update - allowed') : Response::deny('LibroPolicy - update - denied');
    }
}

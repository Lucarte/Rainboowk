<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\User;
use App\Models\Libro;
use Illuminate\Auth\Access\Response;

class LibroPolicy
{
    // true null vs. true false // change this to a real check od admin 'role'
    public function before(User $user)
    {
        return LoginController::isAdmin($user) ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('LibroPolicy - create - allowed') : Response::deny('LibroPolicy - create - denied');
    }

    public function delete(User $user, Libro $libro)
    {
        return $user->id === $libro->user_id ? Response::allow('LibroPolicy - delete - allowed') : Response::deny('LibroPolicy - delete - denied');
    }

    // Only for 'admin'
    public function getAll(User $user, Libro $libro)
    {
        return $user->username === $libro->user_id ? Response::allow('LibroPolicy - getAll - allowed') : Response::deny('LibroPolicy - getAll - denied');
    }

    public function getByTitle(User $user, Libro $libro)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('LibroPolicy - getById - allowed') : Response::deny('LibroPolicy - getById - denied');
    }

    public function update(User $user, Libro $libro)
    {
        return $user->id === $libro->user_id ? Response::allow('LibroPolicy - update - allowed') : Response::deny('LibroPolicy - update - denied');
    }
}

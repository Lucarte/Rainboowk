<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Livre;
use Illuminate\Auth\Access\Response;

class LivrePolicy
{
    // true null vs. true false
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('LivrePolicy - create - allowed') : Response::deny('LivrePolicy - create - denied');
    }

    public function delete(User $user, Livre $livre)
    {
        return $user->id === $livre->user_id ? Response::allow('LivrePolicy - delete - allowed') : Response::deny('LivrePolicy - delete - denied');
    }

    public function list()
    {
        return Response::allow('LivrePolicy - list - allowed');
    }

    public function getByTitle(User $user, Livre $livre)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('LivrePolicy - getById - allowed') : Response::deny('LivrePolicy - getById - denied');
    }

    public function update(User $user, Livre $livre)
    {
        return $user->id === $livre->user_id ? Response::allow('LivrePolicy - update - allowed') : Response::deny('LivrePolicy - update - denied');
    }
}

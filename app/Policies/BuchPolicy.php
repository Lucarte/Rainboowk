<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\User;
use App\Models\Buch;
use Illuminate\Auth\Access\Response;

class BuchPolicy
{
    // true null vs. true false
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user)
    {
        return $user !== null ? Response::allow('BuchPolicy - create - allowed') : Response::deny('BuchPolicy - create - denied');
    }

    public function delete(User $user, Buch $buch)
    {
        return $user->id === $buch->user_id ? Response::allow('BuchPolicy - delete - allowed') : Response::deny('BuchPolicy - delete - denied');
    }

    // Only for 'admin'
    public function list()
    {
        return Response::allow('BuchPolicy - list - allowed');
    }

    public function getByTitle(User $user)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('BuchPolicy - getByTitle - allowed') : Response::deny('BuchPolicy - getByTitle - denied');
    }

    public function update(User $user, Buch $buch)
    {
        return $user->id === $buch->user_id ? Response::allow('BuchPolicy - update - allowed') : Response::deny('BuchPolicy - update - denied');
    }
}

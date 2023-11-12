<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function before(User $user, $ability)
    {
        // Users can perform update, getByUsername, and delete for themselves only
        if (in_array($ability, ['update', 'getByUsername', 'delete']) && request()->route('username') === $user->username) {
            return Response::allow('UserPolicy - allowed');
        }

        // Admin can do all CRUD Ops for all
        return $user->isAdmin() ? Response::allow('UserPolicy - allowed') : null;
    }


    public function usersList(User $user)
    {
        return $user->isAdmin() ? Response::allow('UserPolicy - usersList - allowed') : Response::deny('UserPolicy - delete - denied');
    }

    public function delete(User $user, $username)
    {
        return $user->username === $username ? Response::allow('UserPolicy - delete - allowed') : Response::deny('UserPolicy - delete - denied');
    }

    public function getByUsername(User $user, $username)
    {
        return $user->username === $username ? Response::allow('UserPolicy - get - allowed') : Response::deny('UserPolicy - get - denied');
    }

    public function update(User $user, $username)
    {
        return $user->username === $username ? Response::allow('UserPolicy - update - allowed') : Response::deny('UserPolicy - update - denied');
    }
}

<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\User;
use App\Models\Cover;
use Illuminate\Auth\Access\Response;

class CoverPolicy
{
    // true null vs. true false // change this to a real check od admin 'role'
    public function before(User $user)
    {
        return LoginController::isAdmin($user) ? true : null;
    }

    public function uploadCover(User $user)
    {
        return $user !== null ? Response::allow('CoverPolicy - create - allowed') : Response::deny('CoverPolicy - create - denied');
    }

    public function delete(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id ? Response::allow('CoverPolicy - delete - allowed') : Response::deny('CoverPolicy - delete - denied');
    }

    // Only for 'admin'
    public function getAll(User $user, Cover $cover)
    {
        return $user->username === $cover->user_id ? Response::allow('CoverPolicy - getAll - allowed') : Response::deny('CoverPolicy - getAll - denied');
    }

    public function getByTitle(User $user)
    {
        // Is someone logged in...
        return $user->username !== null ? Response::allow('CoverPolicy - getByTitle - allowed') : Response::deny('CoverPolicy - getByTitle - denied');
    }

    public function update(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id ? Response::allow('CoverPolicy - update - allowed') : Response::deny('CoverPolicy - update - denied');
    }
}

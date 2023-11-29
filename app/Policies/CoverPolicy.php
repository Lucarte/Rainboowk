<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cover;

class CoverPolicy
{
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function deleteCover(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id;
    }

    public function updateCover(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id;
    }
}

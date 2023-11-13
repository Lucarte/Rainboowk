<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cover;
use App\Models\Book;
use App\Models\Buch;
use App\Models\Libro;
use App\Models\Livre;
use Illuminate\Auth\Access\Response;

class CoverPolicy
{
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    private function canUserDeleteCover(User $user, Cover $cover)
    {
        $model = $this->getModelForCover($cover);

        return $user->id === $model->user_id;
    }

    private function getModelForCover(Cover $cover)
    {
        $model = null;

        if ($cover->book_id) {
            $model = Book::findOrFail($cover->book_id);
        } elseif ($cover->libro_id) {
            $model = Libro::findOrFail($cover->libro_id);
        } elseif ($cover->livre_id) {
            $model = Livre::findOrFail($cover->livre_id);
        } elseif ($cover->buch_id) {
            $model = Buch::findOrFail($cover->buch_id);
        }

        return $model;
    }

    public function deleteCover(User $user, Cover $cover)
    {
        return $this->canUserDeleteCover($user, $cover)
            ? Response::allow('CoverPolicy - delete - allowed')
            : Response::deny('CoverPolicy - delete - denied');
    }

    public function updateCover(User $user, Cover $cover)
    {
        return $this->canUserDeleteCover($user, $cover)
            ? Response::allow('CoverPolicy - update - allowed')
            : Response::deny('CoverPolicy - update - denied');
    }
}

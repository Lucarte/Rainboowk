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

    public function deleteCover(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id;
    }

    public function updateCover(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id;
    }

    // public function uploadCover(User $user, Cover $cover)
    // {
    //     return $user->id === $cover->user_id;
    // }

    public function uploadCover($id, User $user, Cover $cover)
    {
        return $user->id === $cover->user_id;

        $book = Book::find($id);
        if ($book) {
            return $book;
        }

        $libro = Libro::find($id);
        if ($libro) {
            return $libro;
        }

        $livre = Livre::find($id);
        if ($livre) {
            return $livre;
        }

        $buecher = Buch::find($id);
        if ($buecher) {
            return $buecher;
        }

        return null; // Item not found
    }
}

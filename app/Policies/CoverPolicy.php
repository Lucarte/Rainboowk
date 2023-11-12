<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\Book;
use App\Models\Buch;
use App\Models\User;
use App\Models\Cover;
use App\Models\Libro;
use App\Models\Livre;
use Illuminate\Auth\Access\Response;

class CoverPolicy
{
    // true null vs. true false // change this to a real check od admin 'role'
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function uploadCover(User $user)
    {
        return $user !== null ? Response::allow('CoverPolicy - create - allowed') : Response::deny('CoverPolicy - create - denied');
    }

    public function deleteCover(User $user, Cover $cover)
    {
        // $book = new Book;
        // $libro = new Libro;
        // $livre = new Livre;
        // $buch = new Buch;

        // $cover->book_id = $book->id ?? $libro->id ?? $livre->id ?? $buch->id;

        if ($cover->book_id) {
            $book = Book::findOrFail($cover->book_id);

            return $user->id === $book->user_id
                ? Response::allow('CoverPolicy - delete - allowed')
                : Response::deny('CoverPolicy - delete - denied');
        } elseif ($cover->libro_id) {
            $libro = Libro::findOrFail($cover->libro_id);
            return $user->id === $libro->user_id
                ? Response::allow('CoverPolicy - delete - allowed')
                : Response::deny('CoverPolicy - delete - denied');
        } elseif ($cover->livre_id) {
            $livre = Livre::findOrFail($cover->livre_id);
            return $user->id === $livre->user_id
                ? Response::allow('CoverPolicy - delete - allowed')
                : Response::deny('CoverPolicy - delete - denied');
        } elseif ($cover->buch_id) {
            $buch = Buch::findOrFail($cover->buch_id);
            return $user->id === $buch->user_id
                ? Response::allow('CoverPolicy - delete - allowed')
                : Response::deny('CoverPolicy - delete - denied');
        }

        // If no association is found, deny the request
        return Response::deny('CoverPolicy - delete - denied');
    }

    public function updateCover(User $user, Cover $cover)
    {
        return $user->id === $cover->user_id ? Response::allow('CoverPolicy - update - allowed') : Response::deny('CoverPolicy - update - denied');
    }
}

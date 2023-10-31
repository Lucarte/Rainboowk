<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Book extends Model
{
    use HasFactory;

    // sowie by posts gibt es ein user_id, by books an author_id USW:
    // to establish these relationships:

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author', 'book_id', 'author_id');
    }

    public function illustrators()
    {
        return $this->belongsToMany(Illustrator::class, 'book_illustrator', 'book_id', 'illustrator_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'book_id', 'id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre', 'book_id', 'genre_id');
    }

    public function getPrintDate(): string
    {
        return date('F, Y', $this->printDate);
    }
}

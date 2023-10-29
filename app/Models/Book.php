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

    public function author()
    {
        return $this->hasMany(Author::class, 'book_id', 'id');
    }

    public function illustrator()
    {
        return $this->hasMany(Illustrator::class, 'book_id', 'id');
    }

    public function publisher()
    {
        return $this->hasMany(Publisher::class, 'book_id', 'id');
    }

    public function genre()
    {
        return $this->hasMany(Genre::class, 'book_id', 'id');
    }
}

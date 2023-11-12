<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buch extends Model
{
    use HasFactory;

    // sowie by posts gibt es ein user_id, by Libros an author_id USW:
    // to establish these relationships:

    public $table = 'buecher';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'buch_author', 'book_id', 'author_id');
    }

    public function illustrators()
    {
        return $this->belongsToMany(Illustrator::class, 'buch_illustrator', 'book_id', 'illustrator_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id', 'id');
    }

    public function cover()
    {
        return $this->hasOne(Cover::class, 'buch_id');
    }

    // public function getPrintDate(): string
    // {
    //     return date('F, Y', $this->printDate);
    // }
}

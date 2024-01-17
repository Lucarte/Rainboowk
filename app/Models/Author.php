<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    public function books()
    {
        return $this->hasMany(Book::class, 'author_id', 'id');
    }

    public function libros()
    {
        return $this->hasMany(Libro::class, 'author_id', 'id');
    }
    
    public function livres()
    {
        return $this->hasMany(Livre::class, 'author_id', 'id');
    }

    public function buecher()
    {
        return $this->hasMany(Buch::class, 'author_id', 'id');
    }


    public function getFullname()
    {
        $fullname = $this->first_name . ' ' . $this->last_name;
        return Str::slug($fullname, '_');
    }

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'date_of_death',
        'biography',
        'nationality',
        'contact_email',
        'website',
        'awards_and_honors',
    ];
}

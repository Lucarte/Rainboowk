<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Illustrator extends Model
{
    public function books()
    {
        return $this->hasMany(Book::class, 'illustrator_id', 'id');
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

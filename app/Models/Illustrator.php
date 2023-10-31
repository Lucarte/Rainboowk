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
}

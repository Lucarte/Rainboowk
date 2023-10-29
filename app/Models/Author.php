<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    public function nichtsicherjetzt()
    {
        return $this->belongsTo(Book::class, 'user_id', 'id');
    }
}

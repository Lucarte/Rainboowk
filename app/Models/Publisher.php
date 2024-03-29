<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    public function books()
    {
        return $this->hasMany(Book::class, 'publisher_id', 'id');
    }

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'website',
        'foundation_year'
    ];
}

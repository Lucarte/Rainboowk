<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cover extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'libro_id',
        'livre_id',
        'buch_id',
        'image_path',
    ];

    // Define one-to-one relationships with different cover types
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function livre()
    {
        return $this->belongsTo(Livre::class, 'livre_id');
    }

    public function buch()
    {
        return $this->belongsTo(Buch::class, 'buch_id');
    }
}

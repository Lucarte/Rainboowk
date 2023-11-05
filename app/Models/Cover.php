<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cover extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        // Add any other common attributes here
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

    // Define common methods or attributes related to covers here
}

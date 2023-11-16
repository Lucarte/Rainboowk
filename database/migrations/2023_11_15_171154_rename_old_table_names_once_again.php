<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('english_books', 'books');
        Schema::rename('spanish_books', 'libros');
        Schema::rename('french_books', 'livres');
        Schema::rename('german_books', 'buecher');
        Schema::rename('english_books_author', 'book_author');
        Schema::rename('english_books_illustrator', 'book_illustrator');
        Schema::rename('spanish_books_author', 'libro_author');
        Schema::rename('spanish_books_illustrator', 'libro_illustrator');
        Schema::rename('french_books_author', 'livre_author');
        Schema::rename('french_books_illustrator', 'livre_illustrator');
        Schema::rename('german_books_author', 'buch_author');
        Schema::rename('german_books_illustrator', 'buch_illustrator');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('books', 'english_books');
        Schema::rename('libros', 'spanish_books');
        Schema::rename('livres', 'french_books');
        Schema::rename('buecher', 'german_books');
        Schema::rename('book_author', 'english_books_author');
        Schema::rename('book_illustrator', 'english_books_illustrator');
        Schema::rename('libro_author', 'spanish_books_author');
        Schema::rename('libro_illustrator', 'spanish_books_illustrator');
        Schema::rename('livre_author', 'french_books_author');
        Schema::rename('livre_illustrator', 'french_books_illustrator');
        Schema::rename('buch_author', 'german_books_author');
        Schema::rename('buch_illustrator', 'german_books_illustrator');
    }
};

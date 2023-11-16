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
        Schema::rename('books', 'english_books');
        Schema::rename('libros', 'spanish_books');
        Schema::rename('livres', 'french_books');
        Schema::rename('buecher', 'german_books');
        Schema::rename('books_author', 'english_books_author');
        Schema::rename('books_illustrator', 'english_books_illustrator');
        Schema::rename('libros_author', 'spanish_books_author');
        Schema::rename('libros_illustrator', 'spanish_books_illustrator');
        Schema::rename('livres_author', 'french_books_author');
        Schema::rename('livres_illustrator', 'french_books_illustrator');
        Schema::rename('buches_author', 'german_books_author');
        Schema::rename('buches_illustrator', 'german_books_illustrator');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('english_books', 'books');
        Schema::rename('spanish_books', 'libros');
        Schema::rename('french_books', 'livres');
        Schema::rename('german_books', 'buecher');
        Schema::rename('english_books_author', 'books_author');
        Schema::rename('english_books_illustrator', 'books_illustrator');
        Schema::rename('spanish_books_author', 'libros_author');
        Schema::rename('spanish_books_illustrator', 'libros_illustrator');
        Schema::rename('french_books_author', 'livres_author');
        Schema::rename('french_books_illustrator', 'livres_illustrator');
        Schema::rename('german_books_author', 'buches_author');
        Schema::rename('german_books_illustrator', 'buches_illustrator');
    }
};

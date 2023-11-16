<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOldTableNames extends Migration
{
    public function up()
    {
        Schema::rename('authors_books', 'books_author');
        Schema::rename('authors_libros', 'libros_author');
        Schema::rename('authors_livres', 'livres_author');
        Schema::rename('authors_buecher', 'buches_author');

        Schema::rename('books_illustrators', 'books_illustrator');
        Schema::rename('illustrators_libros', 'libros_illustrator');
        Schema::rename('illustrators_livres', 'livres_illustrator');
        Schema::rename('buecher_illustrators', 'buches_illustrator');
    }

    public function down()
    {
        Schema::rename('books_author', 'authors_books');
        Schema::rename('libros_author', 'authors_libros');
        Schema::rename('livres_author', 'authors_livres');
        Schema::rename('buches_author', 'authors_buecher');

        Schema::rename('books_illustrator', 'books_illustrators');
        Schema::rename('libros_illustrator', 'illustrators_libros');
        Schema::rename('livres_illustrator', 'illustrators_livres');
        Schema::rename('bueches_illustrator', 'buecher_illustrators');
    }
}

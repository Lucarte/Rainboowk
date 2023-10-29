<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('ISBN')->unique();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('author_id')->index();
            $table->unsignedBigInteger('illustrator_id')->index();
            $table->date('print_date');
            $table->unsignedBigInteger('publisher_id')->index();
            $table->unsignedBigInteger('genre_id')->index();
            $table->string('original_language')->index();
            $table->timestamps();

            // Foreign key constraints with ON UPDATE CASCADE and ON DELETE CASCADE

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('author_id')
                ->references('id')
                ->on('authors')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('illustrator_id')
                ->references('id')
                ->on('illustrators')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('publisher_id')
                ->references('id')
                ->on('publishers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};

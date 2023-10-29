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
        Schema::create('books_german', function (Blueprint $table) {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_german');
    }
};

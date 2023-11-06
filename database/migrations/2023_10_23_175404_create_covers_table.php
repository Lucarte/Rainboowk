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
        Schema::create('covers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id')->nullable();
            $table->unsignedBigInteger('libro_id')->nullable();
            $table->unsignedBigInteger('livre_id')->nullable();
            $table->unsignedBigInteger('buch_id')->nullable();
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('libro_id')->references('id')->on('libros')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('livre_id')->references('id')->on('livres')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('buch_id')->references('id')->on('buecher')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('covers');
    }
};

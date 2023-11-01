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
        Schema::create('illustrators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('fullname')->virtualAs('CONCAT(first_name, " ", last_name)');
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_death')->nullable();
            $table->text('biography')->nullable();
            $table->string('nationality')->nullable();
            $table->string('contact_email')->nullable()->unique();
            $table->string('website')->nullable();
            $table->text('awards_and_honors')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('illustrators');
    }
};

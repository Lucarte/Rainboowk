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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('pronouns')->nullable();
            $table->enum('salutation', ['dear_individual', 'dear_person', 'dear_child', 'Mrs.', 'Mr.']);
            $table->string('username', 16);
            $table->string('email')->unique();
            $table->date('dob')->comment('date of birth');
            $table->timestamp('email_verified_at')->nullable()->default(null);
            $table->enum('locality', ['within_Germany', 'beyond_Germany']);
            $table->enum('personRole', ['author', 'child', 'librarian', 'opposed_to_the_biodiversity', 'publisher_representative', 'activist', 'binary_world_defender', 'journalist', 'curious_person']);
            $table->enum('publicity', ['mouthword', 'online_search', 'other']);
            $table->string('password');
            $table->boolean('terms')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

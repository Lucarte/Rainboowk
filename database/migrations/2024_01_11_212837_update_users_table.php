<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salutation', 'locality', 'personRole', 'publicity']);
        });

        // Recreate columns with modified enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('salutation', ['Dear Individual', 'Dear Person', 'Dear Child', 'Mrs.', 'Mr.']);
            $table->enum('locality', ['Within Germany', 'Beyond Germany']);
            $table->enum('personRole', ['Author', 'Child', 'Librarian', 'Opposed to the Biodiversity', 'Publisher Representative', 'Activist', 'Binary World Defender', 'Journalist', 'Curious Person']);
            $table->enum('publicity', ['Mouthword', 'Online Search', 'Other']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the columns again if needed
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salutation', 'locality', 'personRole', 'publicity']);
        });
    }
};

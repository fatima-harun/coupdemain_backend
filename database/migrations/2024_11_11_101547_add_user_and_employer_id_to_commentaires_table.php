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
        Schema::table('commentaires', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade'); // Candidat
            $table->foreignId('employer_id')->after('user_id')->constrained('users')->onDelete('cascade'); // Employeur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['employer_id']);
            $table->dropColumn(['user_id', 'employer_id']);
        });
    }
};

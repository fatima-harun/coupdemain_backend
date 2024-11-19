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
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidat_id'); // Référence au candidat
            $table->unsignedBigInteger('employer_id'); // Référence à l'employeur
            $table->text('description'); // Contenu du commentaire
            $table->float('note')->nullable(); // Note entre 1 et 5
            $table->timestamps();

            // Clés étrangères
            $table->foreign('candidat_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('employer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Empêcher les doublons : un employeur ne peut commenter un candidat qu'une seule fois
            $table->unique(['candidat_id', 'employer_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};

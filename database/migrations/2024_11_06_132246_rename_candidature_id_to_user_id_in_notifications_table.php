<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer l'ancienne clé étrangère
            $table->dropForeign(['candidature_id']);
            $table->renameColumn('candidature_id', 'user_id');

            // Ajouter la nouvelle clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer la clé étrangère actuelle
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'candidature_id');

            // Restaurer l'ancienne clé étrangère
            $table->foreign('candidature_id')->references('id')->on('candidatures')->onDelete('cascade');
        });
    }
};


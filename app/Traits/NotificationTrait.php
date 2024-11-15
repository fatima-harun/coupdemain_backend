<?php

namespace App\Traits;

use App\Models\Notification;

trait NotificationTrait
{


// Envoie une notification personnalisée à un utilisateur.
// Cette méthode crée une nouvelle notification pour l'utilisateur spécifié
// avec le contenu fourni. Elle utilise la relation entre l'utilisateur et
// ses notifications pour l'envoi.
// * @param \App\Models\User $user
// * @param string $content
// * @return void*/

public function sendNotification($user, $message){
    // Création d'une notification pour l'utilisateur avec le contenu fourni
    $user->notifications()->create(['message' => $message,  // Texte de la notification
    ]);
}

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUserNotifications()
    {
        //récuperer l'utilisateur connecter
        $user = Auth::user();

        //récuperer les notifications associées à cet utilisateur
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        //retourner les notifications associés à cet utiliisateur
        return response()->json([
            'message'=> 'Notifications récupées avec succès',
            'données' => $notifications,
            'status' =>200
        ]);
    }
}

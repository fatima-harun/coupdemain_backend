<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getUserNotifications() {

        $user = Auth::user(); $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return response()->json([ 'message'=> 'Notifications récupérées avec succès',
        'données' => $notifications, 'status' =>200 ]);

    }
    public function markAsRead($notificationId) {

        $user = Auth::user(); $notification = $user->notifications()->find($notificationId);

        if ($notification) { $notification->markAsRead(); return response()->json([ 'message' => 'Notification marquée comme lue', 'status' => 200 ]);

        } return response()->json([ 'message' => 'Notification non trouvée', 'status' => 404 ],

        404);

    }
}

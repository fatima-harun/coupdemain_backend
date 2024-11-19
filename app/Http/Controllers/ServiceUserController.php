<?php

namespace App\Http\Controllers;

use App\Models\ServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceUserController extends Controller
{
    public function store(Request $request)
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Vérifier si l'utilisateur a le rôle demandé
        if (!$user->hasRole('demandeur_d_emploi')) {
            return response()->json([
                'message' => 'Accès non autorisé. Seuls les demandeurs d\'emploi peuvent effectuer cette action.'
            ], 403);
        }

        // Valider les données
        $validatedData = $request->validate([
            'service_id' => 'required|integer|exists:services,id', // Vérifie que le service existe
        ]);

        // Éviter les doublons
        $existing = ServiceUser::where('user_id', $user->id)
                               ->where('service_id', $validatedData['service_id'])
                               ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Vous avez déjà souscrit à ce service.'
            ], 409);
        }

        // Créer un nouveau ServiceUser
        $serviceUser = ServiceUser::create([
            'user_id' => $user->id,
            'service_id' => $validatedData['service_id'],
        ]);

        return response()->json([
            'message' => 'Service ajouté avec succès.',
            'data' => $serviceUser,
        ], 201);
    }
}

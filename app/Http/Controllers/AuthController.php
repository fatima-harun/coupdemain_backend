<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users', 
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:12|unique:users,telephone', // on a mis telephone pour Spécifier la table et la colonne
            'sexe' => 'required|string',
            'CNI' => 'required|string|max:13|unique:users,CNI', 
            'password' => 'required|string|min:8',
        ]);

        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'sexe' => $request->sexe,
            'CNI' => $request->CNI,
            'password' => Hash::make($request->password),
        ]);

        // Réponse de succès
        return response()->json([
            "status" => true,
            "message" => "Utilisateur enregistré avec succès"
        ]);
    }
}

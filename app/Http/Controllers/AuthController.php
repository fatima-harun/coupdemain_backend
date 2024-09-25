<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Méthode privée pour valider les entrées
    private function validateRequest($request, $role)
    {
        return Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:12|unique:users,telephone',
            'sexe' => 'required|in:Féminin,Masculin',
            'CNI' => 'required|string|max:13|unique:users,CNI',
            'role' => 'required|in:employeur,demandeur_d_emploi,admin',
            'password' => 'required|string|min:8',
        ]);
    }

    // Fonction générique pour l'inscription
    public function register(Request $request)
    {
        // Validation des entrées et rôle
        $validator = $this->validateRequest($request, $request->role);

        // Vérification des erreurs de validation
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Création de l'utilisateur
        return $this->createUser($request);
    }

    private function createUser($request)
{
    // Gestion du téléchargement de l'image
    $photoPath = null;
    if ($request->hasFile('photo')) {
        if ($request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('images', 'public');
        } else {
            return response()->json(['error' => 'Invalid photo upload'], 400);
        }
    }

    // Création de l'utilisateur et gestion des erreurs
    try {
        // Créer l'utilisateur avec les données envoyées
        $user = User::create([
            'photo' => $photoPath, // Enregistrement du chemin de l'image
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'sexe' => $request->sexe,
            'CNI' => $request->CNI,
            'password' => Hash::make($request->password),
        ]);

        // Vérifier que le rôle envoyé est valide et l'assigner à l'utilisateur
        $role = $request->role;

        if (in_array($role, ['employeur', 'demandeur_d_emploi', 'admin'])) {
            // Assigner le rôle choisi à l'utilisateur
            $user->assignRole($role);
        } else {
            return response()->json(['error' => 'Rôle invalide'], 400);
        }

        // Retourner une réponse de succès
        return response()->json([
            "status" => true,
            "message" => ucfirst($role) . " enregistré avec succès"
        ]);

    } catch (\Exception $e) {
        // Retourner une réponse d'erreur
        return response()->json([
            'status' => false,
            'error' => "Erreur lors de la création de l'utilisateur: " . $e->getMessage()
        ], 500);
    }
}


    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        if (!$token) {
            return response()->json(['message' => 'Information de connexion incorrectes'], 401);
        }

        return response()->json([
            "access_token" => $token,
            "token_type" => "bearer",
            "user" => auth()->user(),
            "role" => auth()->user()->roles->first()->name ?? 'no_role',  
            "expires_in" => env("JWT_TTL") * 60 . 'seconds'
        ]);
        
    }

     // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Méthode privée pour valider les entrées
    private function validateRequest($request)
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
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:employeur,demandeur_d_emploi,admin', // Ajout de la validation pour le rôle
        ]);
    }

    // Méthode pour créer un utilisateur
    private function createUser($data)
    {
        // Gestion du téléchargement de l'image
        $photoPath = null;
        if (isset($data['photo'])) {
            $photoPath = $data['photo']->store('images', 'public');
        }

        // Création de l'utilisateur
        try {
            $user = User::create([
                'photo' => $photoPath, 
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'adresse' => $data['adresse'],
                'telephone' => $data['telephone'],
                'sexe' => $data['sexe'],
                'CNI' => $data['CNI'],
                'password' => Hash::make($data['password']),
            ]);

            // Assigner le rôle à l'utilisateur
            $user->assignRole($data['role']);

            // Retourner une réponse de succès
            return response()->json([
                "status" => true,
                "message" => ucfirst($data['role']) . " enregistré avec succès"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => "Erreur lors de la création de l'utilisateur."], 500);
        }
    }

    // Fonction d'inscription
    public function register(Request $request)
    {
        // Validation des entrées
        $validator = $this->validateRequest($request);

        // Vérification des erreurs de validation
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Appeler la méthode pour créer l'utilisateur
        return $this->createUser($request->all());
    }

    // Fonction de connexion
    public function login(Request $request)
    {
        // Validation des champs d'email et de mot de passe
        $validator = validator($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string|min:8',
        ]);

        // Si la validation échoue, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Essayer de connecter l'utilisateur avec les credentials fournis
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        // Si la tentative de connexion échoue
        if (!$token) {
            return response()->json(['message' => 'Informations de connexion incorrectes'], 401);
        }

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Vérifier si l'utilisateur a un rôle
        $roleName = $user->roles->isNotEmpty() ? $user->roles[0]->name : 'aucun rôle';

        // Renvoyer la réponse avec le token JWT et les informations de l'utilisateur
        return response()->json([
            "access_token" => $token,
            "token_type" => "bearer",
            "user" => $user,
            "role" => $roleName,
            "expires_in" => env("JWT_TTL") * 60 . ' seconds'
        ]);
    }
}

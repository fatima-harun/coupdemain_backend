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
            'role' => 'required|string|in:employeur,demandeur_d_emploi,admin',
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
        
        $credentials = $request->only('email', 'password');

        \Log::info('Tentative de connexion avec les identifiants : ', $credentials);


        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public function logout()
    {
        auth()->logout();
        return response()->json(["message" => "Déconnexion réussie"]);
    }
    public function refresh()
    {
        try {
            $token = auth()->refresh();
            return response()->json([
                "access_token" => $token,
                "token_type" => "bearer",
                "user" => auth()->user(),
                "expires_in" => (int) env("JWT_TTL") * 60 . " seconds"
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'actualisation du token : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'actualisation du token'], 500);
        }
    }
    

    


   
}

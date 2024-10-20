<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Models\User;
use App\Models\ServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur.
     */
    public function register(AuthRequest $request)
    {
        // Appel direct de la méthode de création après validation via AuthRequest
        return $this->createUser($request);
    }

    /**
     * Création d'un utilisateur avec son rôle et son service s'il est demandeur d'emploi.
     */
    private function createUser($request)
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            if ($request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('images', 'public');
            } else {
                return response()->json(['error' => 'La photo téléchargée est invalide'], 400);
            }
        }

        try {
            $user = User::create([
                'photo' => $photoPath,
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'adresse' => $request->adresse,
                'telephone' => $request->telephone,
                'sexe' => $request->sexe,
                'nom_utilisateur' => $request->nom_utilisateur,
                'password' => Hash::make($request->password),
            ]);

            // Attribution du rôle
            $role = $request->role;
            $user->assignRole($role);

            // Si l'utilisateur est un demandeur d'emploi, lier un service à son profil
            if ($role === 'demandeur_d_emploi') {
                ServiceUser::create([
                    'service_id' => $request->service_id,
                    'user_id' => $user->id,
                ]);
            }

            return response()->json([
                "status" => true,
                "message" => ucfirst($role) . " enregistré avec succès"
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'error' => "Erreur lors de la création de l'utilisateur: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion de l'utilisateur.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('nom_utilisateur', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $user = Auth::user();
        $token = auth()->guard('api')->login($user);
        $roles = $user->getRoleNames();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'roles' => $roles,
            'user' => $user,
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
        ]);
    }
    public function update(UpdateAuthRequest $request, string $id)
    {
        // Trouver l'utilisateur correspondant à l'ID
        $user = User::find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(["message" => "Utilisateur non trouvé"], 404);
        }

        // Récupérer les données validées sauf le champ role
        $data = $request->validated();

        // Retirer le champ 'role' du tableau si présent
        unset($data['role']);

        // Si une photo est téléchargée, gérer l'upload de la nouvelle photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if (File::exists(public_path("storage/" . $user->photo))) {
                File::delete(public_path("storage/" . $user->photo));
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('images', 'public');
            $data['photo'] = $photoPath; // Ajouter le chemin de la nouvelle photo
        }

        // Mettre à jour les informations de l'utilisateur
        $user->update($request->all());

        return response()->json(["message" => "Modification réussie"]);
    }


    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(["message" => "Déconnexion réussie"]);
    }

    /**
     * Rafraîchir le token JWT.
     */
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

    /**
     * Obtenir la liste des utilisateurs et leurs informations selon leur rôle.
     */
    public function employe()
    {
        $user = Auth::user();

        if ($user->hasRole('demandeur_d_emploi')) {
            return User::with(['experiences', 'competences'])
                       ->where('id', $user->id)
                       ->get();
        } elseif ($user->hasRole('employeur')) {
            return User::with(['experiences', 'competences'])
                       ->whereHas('roles', function($query) {
                           $query->where('name', 'demandeur_d_emploi');
                       })
                       ->get();
        }

        return response()->json(['message' => 'Rôle non autorisé'], 403);
    }
    public function profil(Request $request)
    {
        $user = User::with('services','competences','experiences')->find($request->user()->id);
        return response()->json($user);
    }


    public function destroy(string $id){
         // Trouver l'utilisateur correspondant à l'ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'utilisateur non trouvé'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}

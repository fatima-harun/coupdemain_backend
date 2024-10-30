<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceUser;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateAuthRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;


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
            $user->services()->attach($request->service_ids);
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
    public function update(Request $request)
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = Auth::user();


        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(["message" => "Utilisateur non trouvé"], 404);
        }

        // Définir les règles de validation
        $validator = Validator::make($request->all(), [
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nom' => 'sometimes|string',
            'nom_utilisateur'=> 'sometimes|string|unique:users,nom_utilisateur,'.$user->id,
            'prenom' => 'sometimes|string',
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$user->id,
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string|max:12|unique:users,telephone,'.$user->id,
            'sexe' => 'sometimes|in:Féminin,Masculin',
            'password' => 'sometimes|string|min:8',
            'service_id' => 'sometimes|exists:services,id',
        ]);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupérer les données validées
        $data = $validator->validated();

        // Si une photo est téléchargée, gérer l'upload de la nouvelle photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if (File::exists(public_path("storage/" . $user->photo))) {
                File::delete(public_path("storage/" . $user->photo));
            }
            if ($request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('images', 'public');
                $data['photo'] = $photoPath; // Ajouter le chemin de la nouvelle photo
            } else {
                return response()->json(['error' => 'La photo téléchargée est invalide'], 400);
            }
        }

        // Mettre à jour les informations de l'utilisateur
        $user->update($data);

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

    public function getCandidatsByService($serviceId)
    {
        // Récupérer le service par ID
        $service = Service::with(['employe' => function ($query) {
            $query->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'demandeur_d_emploi');
            });
        }])->find($serviceId);

        if (!$service) {
            return response()->json(['message' => 'Service non trouvé'], 404);
        }

        // Récupérer les utilisateurs associés au service ayant le rôle de demandeur d'emploi
        $candidats = $service->employe;

        return response()->json($candidats);
    }


}

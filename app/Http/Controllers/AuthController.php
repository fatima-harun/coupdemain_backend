<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'nom_utilisateur'=> 'required|string|max:12',
            'prenom' => 'required|string',
            'email' => 'nullable|string|email|max:255|unique:users',
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:12|unique:users,telephone',
            'sexe' => 'required|in:Féminin,Masculin',
            'role' => 'required|string|in:employeur,demandeur_d_emploi,admin',
            'password' => 'required|string|min:8',
            'service_id' => 'nullable|exists:services,id',
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
        $photoPath = null;
        if ($request->hasFile('photo')) {
            if ($request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('images', 'public');
            } else {
                return response()->json(['error' => 'Invalid photo upload'], 400);
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

            // $user->services()->attach($serviceId);

            $role = $request->role;
            if (in_array($role, ['employeur', 'demandeur_d_emploi', 'admin'])) {
                $user->assignRole($role);
            } else {
                return response()->json(['error' => 'Rôle invalide'], 400);
            }
            if($role == 'demandeur_d_emploi'){
                $serviceId = $request->service_id;
                ServiceUser::create([
                   'service_id'=>$serviceId,
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

public function login(Request $request)
    {
        // Validation des données
        $validator = validator($request->all(), [
            'nom_utilisateur' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérifier si l'utilisateur existe
        $user = User::where('nom_utilisateur', $request->nom_utilisateur)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], 404); // User not found
        }

        // Vérifier si le mot de passe est correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect',
            ], 401); // Incorrect password
        }

        // Authentification réussie, générer le token
        $token = auth()->guard('api')->login($user);

        // Obtenir les rôles de l'utilisateur
        $roles = $user->getRoleNames();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'roles' => $roles,
            'user' => $user,
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60, // Expiration en secondes
        ]);
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

    public function employe(){

        $user=Auth::user();
        if( $user->hasRole('demandeur_d_emploi')){
            return $employe= User::with(["experiences","competences"])->where('id', $user->id)->get();
        }elseif ($user->hasRole('employeur')) {
            return $employe= User::with(["experiences","competences"])->hasRole('demandeur_d_emploi')->get();
        }
        else{
            return null;
        }

    }


}



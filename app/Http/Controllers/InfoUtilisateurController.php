<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoUtilisateurController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            // 'competence.libelle' => 'required|string',
            // 'competence.description' => 'nullable|string',
            // 'experiences' => 'nullable|array|min:1',
            // 'experiences.*.title' => 'required|string',
            // 'experiences.*.description' => 'nullable|string',
        ]);

        // Enregistrer la compétence
        $competence = new Competence();
        $competence->libelle = $request->competence['libelle'];
        $competence->description = $request->competence['description'];
        $competence->user_id = Auth::id(); // Assurez-vous d'ajouter l'ID de l'utilisateur
        $competence->save();

        // Enregistrer les expériences
        if ($request->has('experiences')) {
            foreach ($request->experiences as $exp) {
                $experience = new Experience();
                $experience->libelle = $exp['title'];
                $experience->description = $exp['description']; // Assurez-vous d'avoir cette colonne
                $experience->user_id = Auth::id(); // Assurez-vous d'ajouter l'ID de l'utilisateur
                $experience->save();
            }
        }

        return response()->json(['message' => 'Compétence et expériences ajoutées avec succès !'], 201);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Code pour lister les ressources si nécessaire
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Code pour afficher une ressource spécifique
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Code pour mettre à jour une ressource spécifique
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Code pour supprimer une ressource spécifique
    }
}

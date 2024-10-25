<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth; // Importer Auth

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($candidatId)
    {
        // Vérifiez si le candidat existe
        $competences = Competence::where('user_id', $candidatId)->get();

        if ($competences->isEmpty()) {
            return response()->json(['message' => 'Aucune compétence trouvée pour ce candidat'], 404);
        }

        return response()->json($competences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'libelle' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Enregistrer la compétence
        $competence = new Competence();
        $competence->libelle = $request->libelle;
        $competence->description = $request->description;
        $competence->user_id = Auth::id();
        $competence->save();

        return response()->json($competence, 201); // Retournez la compétence créée
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $competence = Competence::find($id);

        if (!$competence) {
            return response()->json(['message' => 'Compétence non trouvée'], 404);
        }

        return response()->json($competence); // Retournez la compétence trouvée
    }

    public function usercompetence()
{
    // Récupérer l'utilisateur connecté
    $userId = Auth::id();

    $competences = Competence::where('user_id', $userId)->get();

    if ($competences->isEmpty()) {
        return response()->json(['message' => 'Aucune compétence trouvée pour cet utilisateur'], 404);
    }

    // Retourner les compétences dans une réponse JSON
    return response()->json([
        'competences' => $competences,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $competence = Competence::find($id);

        if (!$competence) {
            return response()->json(['message' => 'Compétence non trouvée'], 404);
        }

        $request->validate([
            'libelle' => 'required|string',
            'description' => 'nullable|string', 
        ]);

        $competence->update($request->all());
        return response()->json($competence); // Retournez la compétence mise à jour
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $competence = Competence::find($id);
        if (!$competence) {
            return response()->json(['message' => 'Compétence non trouvée'], 404);
        }

        $competence->delete();
        return response()->json(['message' => 'Compétence supprimée avec succès']);
    }
}

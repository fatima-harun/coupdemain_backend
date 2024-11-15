<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth; // Importer Auth

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($candidatId)
    {
        // Vérifiez si le candidat existe
        $experiences = Experience::where('user_id', $candidatId)->get();

        if ($experiences->isEmpty()) {
            return response()->json(['message' => 'Aucune expérience trouvée pour ce candidat'], 404);
        }

        return response()->json($experiences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            // 'libelle' => 'required|string',
            // 'description' => 'nullable|string',
        ]);

        // Enregistrer la compétence
        $experience = new Experience();
        $experience->libelle = $request->libelle;
        $experience->description = $request->description;
        $experience->user_id = Auth::id();
        $experience->save();

        return response()->json($experience, 201); // Retournez l'experience créée
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experience = Experience::find($id);

        if (!$experience) {
            return response()->json(['message' => 'experience non trouvée'], 404);
        }

        return response()->json($experience); // Retournez la experience trouvée
    }

    public function userexperience()
{
    // Récupérer l'utilisateur connecté
    $userId = Auth::id();

    $experiences = Experience::where('user_id', $userId)->get();

    if ($experiences->isEmpty()) {
        return response()->json(['message' => 'Aucune experience trouvée pour cet utilisateur'], 404);
    }

    // Retourner les experiences dans une réponse JSON
    return response()->json([
        'experiences' => $experiences,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $experience = Experience::find($id);

        if (!$experience) {
            return response()->json(['message' => 'experience non trouvée'], 404);
        }

        $request->validate([
            'libelle' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $experience->update($request->all());
        return response()->json($experience); // Retournez la experience mise à jour
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $experience = Experience::find($id);
        if (!$experience) {
            return response()->json(['message' => 'experience non trouvée'], 404);
        }

        $experience->delete();
        return response()->json(['message' => 'experience supprimée avec succès']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Commentaire::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addComment(Request $request, $userId)
    {
        $employeurId = Auth::id(); //employeur connecté

        $request->validate([
            'description' => 'required|string'
        ]);

        // Vérifier si un commentaire existe déjà pour cet employeur et ce candidat
        $existingComment = Commentaire::where('employer_id', $employeurId)->where('user_id', $userId)->first();

        if ($existingComment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà commenté ce candidat.'
            ], 403);
        }

        // Créer un nouveau commentaire
        $comment = Commentaire::create([
            'user_id' => $userId,
            'employer_id' => $employeurId,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès.',
            'comment' => $comment
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($userId)
    {
        // Récupérer les commentaires destinés au candidat spécifique avec l'ID $userId
        $recommendations = Commentaire::where('user_id', $userId) // Filtrer par le candidat ciblé
            ->with('employer') // Charger les informations de l'employeur qui a fait le commentaire
            ->get();

        return response()->json($recommendations);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Trouver le commentaire par ID
        $commentaire = Commentaire::find($id);

        // Vérifier si le commentaire existe
        if(!$commentaire){
            return response()->json(['message'=>'Commentaire non trouvé'], 404);
        }

        // Vérifier que l'employeur connecté est celui qui a posté ce commentaire
        $employeurId = Auth::id();
        if ($commentaire->employer_id !== $employeurId) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'], 403);
        }

        // Validation de la requête
        $request->validate([
            'description' => 'required|string|max:500', // Modifier la longueur ou d'autres contraintes selon le besoin
        ]);

        // Mettre à jour le commentaire
        $commentaire->update([
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire mis à jour avec succès.',
            'comment' => $commentaire
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Trouver le commentaire par ID
        $commentaire = Commentaire::find($id);

        // Vérifier si le commentaire existe
        if(!$commentaire){
            return response()->json(['message'=>'Commentaire non trouvé'], 404);
        }

        // Vérifier que l'employeur connecté est celui qui a posté ce commentaire
        $employeurId = Auth::id();
        if ($commentaire->employer_id !== $employeurId) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'], 403);
        }

        // Supprimer le commentaire
        $commentaire->delete();

        return response()->json(['message' => 'Commentaire supprimé avec succès']);
    }
}


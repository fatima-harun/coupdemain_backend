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
     // Afficher les commentaires pour un candidat donné
     public function index($candidat_id)
     {
         $commentaires = Commentaire::where('candidat_id', $candidat_id)
             ->with('employer') // Inclure les informations de l'employeur
             ->get();

         return response()->json($commentaires);
     }

     // Ajouter un commentaire et une note
     public function store(Request $request)
     {
         $request->validate([
            //  'candidat_id' => 'required|exists:users,id',
            //  'contenu' => 'required|string|max:255',
            //  'note' => 'nullable|numeric|min:1|max:5',
         ]);

         // Vérifier si l'employeur a déjà commenté ce candidat
         $existing = Commentaire::where('candidat_id', $request->candidat_id)
             ->where('employer_id', Auth::id())
             ->first();

         if ($existing) {
             return response()->json(['message' => 'Vous avez déjà commenté ce candidat.'], 403);
         }

         Commentaire::create([
             'candidat_id' => $request->candidat_id,
             'employer_id' => Auth::id(),
             'description' => $request->description,
             'note' => $request->note,
         ]);

         return response()->json(['message' => 'Commentaire ajouté avec succès.']);
     }

     // Modifier un commentaire
     public function update(Request $request, $id)
     {
         $request->validate([
             'description' => 'required|string|max:255',
             'note' => 'nullable|numeric|min:1|max:5',
         ]);

         $commentaire = Commentaire::findOrFail($id);

         // Vérifier que l'utilisateur est l'auteur du commentaire
         if ($commentaire->employer_id !== Auth::id()) {
             return response()->json(['message' => 'Action non autorisée.'], 403);
         }

         $commentaire->update([
             'description' => $request->description,
             'note' => $request->note,
         ]);

         return response()->json(['message' => 'Commentaire modifié avec succès.']);
     }

     // Supprimer un commentaire
     public function destroy($id)
     {
         $commentaire = Commentaire::findOrFail($id);

         // Vérifier que l'utilisateur est l'auteur du commentaire
         if ($commentaire->employer_id !== Auth::id()) {
             return response()->json(['message' => 'Action non autorisée.'], 403);
         }

         $commentaire->delete();

         return response()->json(['message' => 'Commentaire supprimé avec succès.']);
     }
}


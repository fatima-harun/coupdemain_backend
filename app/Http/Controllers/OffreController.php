<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Requests\OffreRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateOffreRequest;

class OffreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offres = Offre::with('services')->get();

        return response()->json(['data' => $offres]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OffreRequest $request)
{
    try {
        $user_id = auth()->user()->id;

        // Créer une nouvelle offre
        $offre = Offre::create([
            'description' => $request->description,
            'nombre_postes' => $request->nombre_postes,
            'lieu' => $request->lieu,
            'salaire' => $request->salaire,
            'horaire' => $request->horaire,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'profil' => $request->profil,
            'date_limite' => $request->date_limite,
            'user_id' => $user_id,
        ]);

        // Associer l'offre au service via la table pivot
        $offre->services()->attach($request->service_ids);

        // Retourner une réponse JSON en cas de succès
        return response()->json([
            'success' => true,
            'message' => 'Offre ajoutée avec succès',
            'data' => $offre
        ], 201); // 201 : Création réussie

    } catch (\Exception $e) {
        // Gestion des erreurs en cas d'échec
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'ajout de l\'offre.',
            'error' => $e->getMessage()
        ], 500); // 500 : Erreur interne du serveur
    }
}

    /**
     * Display the specified resource.
     */
   public function show(string $id)
{
    // Récupérer l'offre avec ses services associés
    $offre = Offre::with('services')->find($id);

    if (!$offre) {
        return response()->json(['message' => 'Offre non trouvée'], 404);
    }

    return response()->json(['data' => $offre]);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOffreRequest $request, string $id)
{
    // Trouver l'offre par ID
    $offre = Offre::find($id);

    if (!$offre) {
        return response()->json(['message' => 'Offre non trouvée'], 404);
    }

    // Vérifier que l'utilisateur actuel est le propriétaire de l'offre
    if ($offre->user_id !== auth()->id()) {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette offre'], 403);
    }

    // Mettre à jour les autres champs de l'offre
    $offre->update($request->except('service_ids')); // Exclure les services de la mise à jour directe

    // Vérifier si des services sont fournis
    if ($request->has('service_ids')) {
        $newServiceIds = $request->input('service_ids', []); // Récupérer les services soumis
        // Synchroniser les services avec ceux fournis par l'utilisateur
        $offre->services()->sync($newServiceIds);
    }

    // Charger les relations pour renvoyer une réponse complète
    $offre->load('services');

    return response()->json(['data' => $offre], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    // Trouver l'offre par ID
    $offre = Offre::find($id);

    if (!$offre) {
        return response()->json(['message' => 'Offre non trouvée'], 404);
    }

    // Vérifier que l'utilisateur actuel est le propriétaire de l'offre
    if ($offre->user_id !== auth()->id()) {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer cette offre'], 403);
    }

    // Supprimer l'offre
    $offre->delete();

    return response()->json(['message' => 'Offre supprimée avec succès']);
}

    public function getOffresByService($serviceId)
    {
        // Récupérer le service par ID
        $service = Service::with('offres')->find($serviceId);

        if (!$service) {
            return response()->json(['message' => 'Service non trouvé'], 404);
        }

        // Récupérer les offres associées au service
        $offres = $service->offres;

        return response()->json($offres);
    }

    public function ShowMesOffres() {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Vérifier si l'utilisateur a le rôle d'employeur
        if (!$user->hasRole('employeur')) {
            return response()->json(['message' => 'Accès interdit. Vous n\'avez pas le droit de voir ces offres.'], 403);
        }
        // Récupérer toutes les offres créées par cet utilisateur avec leurs services associés
        $offres = Offre::with('services')->where('user_id', $user->id)->get();
        // Vérifier si l'utilisateur a des offres
        if ($offres->isEmpty()) {
            return response()->json(['message' => 'Aucune offre trouvée'], 404);
        }

        return response()->json(['data' => $offres]);
    }

}



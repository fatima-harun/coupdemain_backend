<?php

namespace App\Http\Controllers;

use App\Http\Requests\OffreRequest;
use App\Http\Requests\UpdateOffreRequest;
use App\Models\Offre;
use App\Models\Service;
use Illuminate\Http\Request;

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

    // Mettre à jour l'offre avec les données validées
    $offre->update($request->except('service_ids')); // Exclure service_ids de la mise à jour

    // Vérifiez si service_ids est présent dans la requête
    if ($request->has('service_ids')) {
        // Récupérer les IDs des services existants
        $existingServices = $offre->services()->pluck('services.id')->toArray(); // Spécifiez le nom de la table ici

        // Fusionner les services existants avec les nouveaux services
        $newServiceIds = array_unique(array_merge($existingServices, $request->service_ids));

        // Synchroniser les services associés à l'offre
        $offre->services()->sync($newServiceIds); // Utilisez sync pour mettre à jour les relations
    }

    return response()->json(['data' => $offre]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $offre = Offre::find($id);
        if (!$offre) {
            return response()->json(['message' => 'offre non trouvé'], 404);
        }

        $offre->delete();
        return response()->json(['message' => 'offre supprimée avec succès']);
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
}



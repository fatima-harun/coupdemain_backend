<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;

        // Validation des données
        $request->validate([
            'description' => 'required|string|max:200',
            'lieu' => 'required|string|max:20',
            'salaire' => 'required|numeric|max:99999',
            'nombre_postes' => 'required|numeric|max:99999',
            'horaire' => 'required|string|max:10',
            'date_debut' => 'required|date_format:Y-m-d',
            'date_fin' => 'nullable|date_format:Y-m-d',
            'date_limite' => 'nullable|date_format:Y-m-d',
            'profil' => 'required|string|max:300',
            'service_id' => 'required|exists:services,id',
        ]);

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
        $offre->services()->attach($request->service_id);

        return response()->json([
            'success' => true,
            'message' => 'Offre ajoutée avec succès',
            'data' => $offre
        ]);
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
    public function update(Request $request, string $id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'offre non trouvé'], 404);
        }

        // Validation des données (décommenter si nécessaire)
        $request->validate([
            // 'description' => 'required|string|max:200',
            // 'lieu' => 'required|string|max:20',
            // 'salaire' => 'required|numeric|max:99999',
            // 'nombre_postes' => 'required|numeric|max:99999',
            // 'horaire' => 'required|string|max:10',
            // 'date_debut' => 'required|date_format:Y-m-d',
            // 'date_fin' => 'nullable|date_format:Y-m-d',
            // 'date_limite' => 'nullable|date_format:Y-m-d',
            // 'profil' => 'required|string|max:300',
            // 'service_id' => 'required|exists:services,id',
        ]);

        $offre->update($request->all());
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



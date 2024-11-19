<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Notification;

class CandidatureController extends Controller
{
     use NotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validation des données entrantes
    $request->validate([
        'offre_id' => 'required|exists:offres,id',
       'statut' => 'required|in:en cours,rejeter,recruter',
    ]);

    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Vérifier si l'utilisateur est un employeur
    if ($user->hasRole('employeur')) {
        return response()->json([
            'message' => 'Les employeurs ne peuvent pas postuler à des offres.',
        ], 403);
    }

    // Vérifier si l'utilisateur a déjà postulé à cette offre
    $existingCandidature = Candidature::where('user_id', $user->id)
        ->where('offre_id', $request->offre_id)
        ->first();

    if ($existingCandidature) {
        return response()->json([
            'message' => 'Vous avez déjà postulé à cette offre.',
        ], 409);
    }

    // Créez la candidature
    try {
        // Récupération et création de la candidature
        $candidature = Candidature::create([
            'user_id' => $user->id,
            'offre_id' => $request->offre_id,
            'statut' => $request->statut,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $e->errors(), // Affiche les erreurs de validation
        ], 422);
    }

   // Créer une notification pour l'utilisateur
//    Notification::create([ 'user_id' => $request->user()->id,
//    'message' => 'Vous avez postulé à l\'offre ' . $offre->titre, ]);

//    // Créer une notification pour l'employeur
//    Notification::create([ 'user_id' => $offre->employeur_id,
//    'message' => $request->user()->name . ' a postulé pour l\'offre ' . $offre->titre, ]);


}


    public function getNotifications()
{
    $notifications = auth()->user()->notifications;
    return response()->json($notifications);
}
public function lu($id)
{
    // Vérifie si la notification existe et appartient à l'utilisateur connecté
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if ($notification) {
        // Marque la notification comme lue (mettre le champ 'read' à true)
        $notification->read = true;
        $notification->save();

        return response()->json([
            'message' => 'Notification marquée comme lue.'
        ], 200);
    }

    return response()->json([
        'message' => 'Notification non trouvée ou non autorisée.'
    ], 404);
}

    public function getCandidaturesByOffre($offreId)

    {
        return Candidature::with('user')->where('offre_id', $offreId)->get();
        // Récupérer les candidatures associées à l'ID de l'offre
        $candidatures = Candidature::where('offre_id', $offreId)->get();

        // Retourner les candidatures au format JSON
        return response()->json([
            'data' => $candidatures,
            'message' => 'Candidatures récupérées avec succès.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatut(Request $request, $id)
{
    $candidature = Candidature::findOrFail($id);
    $candidature->statut = $request->input('statut');
    $candidature->save();

    // Créer une notification pour l'utilisateur
    Notification::create([ 'user_id' => $candidature->user_id,

    'message' => 'Le statut de votre candidature pour ' . $candidature->offre->titre . ' a changé en ' . $candidature->statut, ]);

    // Retourne les candidatures mises à jour
    $candidatures = Candidature::all();

    return response()->json([
        'message' => 'Statut mis à jour avec succès',
        'candidatures' => $candidatures
    ], 200);
}
    /**
     *Récupère l'ensemble des utilisateurs qui ont été recrutés
     */
    public function getRecruter()
{
    $totalrecrute = Candidature::count();
    $nombreRecrute = Candidature::where('statut', 'recruter')->count();
    $nonrecrute = $totalrecrute - $nombreRecrute;  // Calcul des non-recrutés

    return response()->json([
        'nombre_recrute' => $nombreRecrute,
        'nombre_non_recrute' => $nonrecrute,
    ]);
}

}

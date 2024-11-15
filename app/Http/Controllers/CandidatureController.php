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
    $request->validate([
        'offre_id' => 'required|exists:offres,id',
        'statut' => 'required|in:en cours,rejeter,recruter',
    ]);

    // Créez la candidature
    $candidature = Candidature::create([
        'user_id' => auth()->user()->id,
        'offre_id' => $request->offre_id,
        'statut' => $request->statut,
    ]);

    // Récupérez les détails de l'offre associée
    $offre = $candidature->offre;

    // Vérifiez si l'utilisateur a bien créé la candidature
    if ($offre && $candidature->user_id == auth()->user()->id) {
        // Si c'est l'utilisateur connecté, envoyez la notification
        $message = 'Votre candidature pour l\'offre "' . $offre->description . '" a été enregistrée avec succès.';
        $this->sendNotification(auth()->user(), $message);
    }

    return response()->json([
        'message' => 'Candidature enregistrée avec succès.',
        'offre' => $offre
    ], 201);
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function getRecuter()
    {
        $recruitedCount = Candidature::where('statut', 'recruter')->count();

        return response()->json([
            'recruited_count' => $recruitedCount
        ]);
    }

}

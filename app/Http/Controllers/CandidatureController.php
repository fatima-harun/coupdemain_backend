<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\NotificationTrait;

class CandidatureController extends Controller
{
    // use NotificationTrait;
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
        //     'offre_id' => 'required|exists:offres,id',
        //     'date_candidature' => 'required|date',
        //    'statut' => 'required|in:en cours,rejeter,recruter'
        ]);
    
        // Créez la candidature
        $candidature = Candidature::create([
            'user_id' => auth()->user()->id,
            'offre_id' => $request->offre_id,
            'statut' => $request->statut,
        ]);
    
        // Récupérez les détails de l'offre associée
        $offre = $candidature->offre; //offre associée
        $user = $candidature->user; //candidat associé
       //$notification = $candidature->notification; // Notification associée
    
        // Envoyez une notification à l'utilisateur connecté
        $message = 'Votre candidature pour l\'offre "' . $offre->titre . '" a été enregistrée avec succès.';
        // $this->sendNotification(auth()->user(), $message);
    
        return response()->json([
            'message' => 'Candidature enregistrée avec succès.',
            'offre' => $offre
        ], 201);
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
        return Candidature::with('user') 
        ->where('offre_id', $offreId)
        ->get();
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

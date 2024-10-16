<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\NotificationTrait;

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
            'date_candidature' => 'required|date'
        ]);

        // Créez la candidature
        $candidature = Candidature::create([
            'user_id' => auth()->user()->id,
            'offre_id' => $request->offre_id,
            'date_candidature' => $request->date_candidature,
        ]);

        // Envoyez une notification à l'utilisateur connecté
        $message = 'Votre candidature pour l\'offre ID ' . $request->offre_id . ' a été enregistrée avec succès.';
        $this->sendNotification(auth()->user(), $message);

        return response()->json(['message' => 'Candidature enregistrée avec succès.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

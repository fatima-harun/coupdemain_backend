<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;


class CandidatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::role('demandeur_d_emploi')->with('services')->get();

       return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($candidatId)
{
    // Récupérer le candidat avec tous ses services associés
    $candidat = User::with('services','competences', 'experiences')->find($candidatId);

    // Vérifier si le candidat existe
    if (!$candidat) {
        return response()->json(['message' => 'Candidat non trouvé'], 404);
    }

    // Retourner les détails du candidat avec ses services
    return response()->json($candidat);
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

<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($candidatId)
{
    // Vérifiez si le candidat existe
    $competences = Competence::where('user_id', $candidatId)->get();

    if ($competences->isEmpty()) {
        return response()->json(['message' => 'Aucune compétence trouvée pour ce candidat'], 404);
    }

    return response()->json($competences);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'libelle'=> 'required|string',
            ]

         );
         return Competence::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $competence = Competence::find($id);

        if(!$competence){
            return response()->json(['message'=>'competence non trouvée'], 404);

            return $competence;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $competence = Competence::find($id);

        if(!$competence){
            return response()->json(['message'=>'competence non trouvée'], 404);
        }

        $request->validate(
            [
                // 'libelle'=> 'required|string',
                // 'description'=> 'required|string',
            ]
         );
         $competence->update($request->all());
         return $competence;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $competence = Competence::find($id);
        if(!$competence){
            return response()->json(['message'=>'competence non trouvé'], 404);
        }

        $competence->delete();
        return response()->json(['message'=>'competence supprimé avec succés']);
    }
}

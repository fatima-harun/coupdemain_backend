<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($candidatId)
{
    // Vérifiez si le candidat existe
    $experiences = Experience::where('user_id', $candidatId)->get();

    if ($experiences->isEmpty()) {
        return response()->json(['message' => 'Aucune experience trouvée trouvée pour ce candidat'], 404);
    }

    return response()->json($experiences);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'libelle'=> 'required|string',
                'description'=> 'required|string',
            ]

         );
         return Experience::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experience = Experience::find($id);

        if(!$experience){
            return response()->json(['message'=>'competence non trouvée'], 404);

            return $experience;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $experience = Experience::find($id);

        if(!$experience){
            return response()->json(['message'=>'experience non trouvée'], 404);
        }

        $request->validate(
            [
                // 'libelle'=> 'required|string',
                // 'description'=> 'required|string',
            ]
         );
         $experience->update($request->all());
         return $experience;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $experience = Experience::find($id);
        if(!$experience){
            return response()->json(['message'=>'experience non trouvée'], 404);
        }
        $experience->delete();
        return response()->json(['message'=>'experience supprimée avec succés']);
    }
}

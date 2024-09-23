<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Competence::all();
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
        $competence = Competences::find($id);

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
        $competence = Competences::find($id);

        if(!$competence){
            return response()->json(['message'=>'competence non trouvée'], 404);
        }

        $request->validate(
            [
                'libelle'=> 'required|string',
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
        $competence = Competences::find($id);
        if(!$competence){
            return response()->json(['message'=>'competence non trouvé'], 404);
        }

        $competence->delete();
        return response()->json(['message'=>'competence supprimé avec succés']);
    }
}

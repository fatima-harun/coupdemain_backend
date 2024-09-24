<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Experience::all();
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
            return response()->json(['message'=>'competence non trouvée'], 404);
        }

        $request->validate(
            [
                'libelle'=> 'required|string',
                'description'=> 'required|text',
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
        $competence = Competences::find($id);
        if(!$competence){
            return response()->json(['message'=>'experience non trouvée'], 404);
        }

        $competence->delete();
        return response()->json(['message'=>'experience supprimée avec succés']);
    }
}

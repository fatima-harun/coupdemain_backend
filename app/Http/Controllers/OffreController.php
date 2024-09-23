<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offre;

class OffreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return offre::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'description'=> 'required|string|max:200',
                'lieu'=> 'required|string|max:20',
                'salaire'=> 'required|string|max:20',
                'horaire'=> 'required|string|max:10',
                'datedebut'=> 'required|string|max:10',
                'profil'=> 'required|string|max:300',
            ]
            
         );
         return Offre::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $offre = Offre::find($id);

        if(!$offre){
            return response()->json(['message'=>'offre non trouvé'], 404);

            return $offre;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $offre = Offre::find($id);

        if(!$offre){
            return response()->json(['message'=>'offre non trouvé'], 404);
        }

        $request->validate(
            [
                'description'=> 'required|string|max:200',
                'lieu'=> 'required|string|max:20',
                'salaire'=> 'required|string|max:20',
                'horaire'=> 'required|string|max:10',
                'datedebut'=> 'required|string|max:10',
                'profil'=> 'required|string|max:300',
            ]
         );
         $offre->update($request->all());
         return $offre;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $offre = Commentaires::find($id);
        if(!$offre){
            return response()->json(['message'=>'offre non trouvé'], 404);
        }

        $offre->delete();
        return response()->json(['message'=>'offre supprimé avec succés']);
    }
    }


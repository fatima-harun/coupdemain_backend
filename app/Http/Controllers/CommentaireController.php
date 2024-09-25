<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Commentaires;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Commentaire::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'description'=> 'required|string|max:50',
            ]
         );
         return Commentaire::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $commentaire = Commentaire::find($id);

        if(!$commentaire){
            return response()->json(['message'=>'commentaire non trouvé'], 404);

            return $commentaire;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $commentaire = Commentaire::find($id);

        if(!$commentaire){
            return response()->json(['message'=>'commentaire non trouvé'], 404);
        }

        $request->validate(
            [
                'auteur'=> 'required|string|max:20',
                'description'=> 'required|string|max:50',
            ]
         );
         $commentaire->update($request->all());
         return $commentaire;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $commentaire = Commentaire::find($id);
        if(!$commentaire){
            return response()->json(['message'=>'commentaire non trouvé'], 404);
        }

        $commentaire->delete();
        return response()->json(['message'=>'commentaire supprimé avec succés']);
    }
}

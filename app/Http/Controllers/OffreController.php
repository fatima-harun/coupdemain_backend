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
        $user_id = auth()->user()->id;  
        $request->validate(
            [
                'description' => 'required|string|max:200',
                'lieu' => 'required|string|max:20',
                'salaire' => 'required|numeric|max:99999', 
                'nombre_postes' => 'required|numeric|max:99999', 
                'horaire' => 'required|string|max:10',
                'date_debut' => 'required|date_format:Y-m-d', 
                'date_fin' => 'nullable|date_format:Y-m-d', 
                'date_limite' => 'nullable|date_format:Y-m-d', 
                'profil' => 'required|string|max:300',
            ],
            
         );
        
         // Créer une nouvelle offre
    $offre = Offre::create([
        'description'=> $request->description,
        'nombre_postes' => $request->nombre_postes,
        'lieu'=> $request->lieu,
        'salaire'=> $request->salaire,
        'horaire'=> $request->horaire,
        'date_debut'=> $request->date_debut,
        'date_fin'=> $request->date_fin,
        'profil'=> $request->profil,
        'date_limite'=> $request->date_limite,
        'user_id' => $user_id,

    ]);

    // Associer l'ID de l'utilisateur connecté
   
    // L'ID de l'utilisateur connecté
    $offre->save();  // Sauvegarder l'offre mise à jour

    return response()->json([
        'success' => true,
        'message' => 'Offre ajoutée avec succès',
        'data' => $offre
    ]);
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
                // 'description' => 'required|string|max:200',
                // 'lieu' => 'required|string|max:20',
                // 'salaire' => 'required|numeric|max:99999', 
                // 'horaire' => 'required|string|max:10',
                // 'date_debut' => 'required|date_format:Y-m-d', 
                // 'date_fin' => 'nullable|date_format:Y-m-d', 
                // 'date_limite' => 'nullable|date_format:Y-m-d', 
                // 'profil' => 'required|string|max:300',
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
        $offre = Offre::find($id);
        if(!$offre){
            return response()->json(['message'=>'offre non trouvé'], 404);
        }

        $offre->delete();
        return response()->json(['message'=>'offre supprimé avec succés']);
    }
    }


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service=Service::all();
        return response()->json(['data' =>$service]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'libelle'=> 'required|string',
                'description'=> 'required|string'
            ]

         );
         return Service::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);

        if(!$service){
            return response()->json(['message'=>'service non trouvé'], 404);
        }

        return $service;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::find($id);

        if(!$service){
            return response()->json(['message'=>'service non trouvé'], 404);
        }

        $request->validate(
            [
                'libelle'=> 'required|string',
                'description'=> 'required|string',
            ]
         );
         $service->update($request->all());
         return $service;
    }

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message'=>'service non trouvé'], 404);
        }

        $service->delete();
        return response()->json(['message'=>'service supprimé avec succés']);
    }
}

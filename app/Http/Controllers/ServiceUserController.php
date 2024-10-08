<?php

namespace App\Http\Controllers;

use App\Models\ServiceUser;
use Illuminate\Http\Request;

class ServiceUserController extends Controller
{
    public function store(Request $request){

        $user = Auth::user();

        $request->validate(
            [
                'user_id'=>$user->id,
                'service_id'=> 'required|integer',
            ]
         );
        if($user->role == 'demandeur_d_emploi'){
            return ServiceUser::create($request->all());
        }
    }
}

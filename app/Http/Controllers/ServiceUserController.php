<?php

namespace App\Http\Controllers;

use App\Models\ServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceUserController extends Controller
{
    public function store(Request $request){

        $user = Auth::user();
        $roles = $user->getRoleNames();
        $request->validate(
            [
                'user_id'=>$user->id,
                'service_id'=> 'required|integer',
            ]
         );
        if($role == 'demandeur_d_emploi'){
            return ServiceUser::create($request->all());
        }
    }
}

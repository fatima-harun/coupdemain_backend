<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
             'nom'=>"diop",
             'prenom'=>"fabi",
             'email'=>"fabi@gmail.com",
             'adresse'=>"sonatel",
             'telephone'=>"782710111",
             'CNI'=>"1234567896541",
             'statut'=>"employeur",
             'email_verified_at'=>now(),
             'password'=>Hash::make('password'),
             'remember_token'=>str::random(10),
            ],


            [
                'nom'=>"lo",
                'prenom'=>"sophie",
                'email'=>"sophie@gmail.com",
                'adresse'=>"liberte 6",
                'telephone'=>"701245879",
                'CNI'=>"1234327891541",
                'statut'=>"demandeur_d_emploi",
                'email_verified_at'=>now(),
                'password'=>Hash::make('password'),
                'remember_token'=>str::random(10),
               ],
   
               [
                'nom'=>"dia",
                'prenom'=>"david",
                'email'=>"david@gmail.com",
                'adresse'=>"sacre coeur",
                'telephone'=>"772245879",
                'CNI'=>"3537627891541",
                'statut'=>"admin",
                'email_verified_at'=>now(),
                'password'=>Hash::make('password'),
                'remember_token'=>str::random(10),
               ],
        ];
        foreach($users as $user){
            User::create($user);
        }
    }
}

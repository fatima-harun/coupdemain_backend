<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
           [
                'libelle' =>"Chauffeur"
           ],

           [
            'libelle' =>"Aide ménagère"
           ],

           [
            'libelle' =>"Cuisinier(e)"
           ],

           [
            'libelle' =>"Gardien(ne)"
           ],

           [
            'libelle' =>"Nounou"
           ],
        ];
        foreach($services as $service){
            Service::create($service);
        }
    }
}

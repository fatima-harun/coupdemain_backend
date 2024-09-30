<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
        //    permissions de l'admin
        'se connecter',
        'desactiver un compte',
        'activer un compte',
        'valider les annonces des employeurs',
        'refuser les annonces des employeurs',
        'supprimer les commentaires innapropriés',
        'modifier les categories de services',
        'ajouter les categories de services',
        'supprimer une categorie de services',
        "se deconnecter",

        //    permissions de l'employeur
        "s'inscrire",
        'se connecter',
        'publier des annonces',
        'accepter une candidature',
        'rejeter une candidature',
        'voir la liste des candidatures',
        'voir la liste des candidats au niveau de la plateforme',
        'donner des recommandations',
        'evaluer les anciens employes',
        "se deconnecter",

         //    permissions de l'employé
         "s'inscrire",
        "se connecter",
        "rechercher des offres d'emploi",
        "consulter une offre d'emploi",
        "postuler à une offre d'emploi",
        "enregistrer les offres d'emplois",
        "se deconnecter"
];
foreach ($permissions as $permission) {
    Permission::firstOrCreate( ['name' => $permission, 'guard_name' => 'api']);
}

   $roles = [
      'admin' => [
       'se connecter',
        'desactiver un compte',
        'activer un compte',
        'valider les annonces des employeurs',
        'refuser les annonces des employeurs',
        'supprimer les commentaires innapropriés',
        'modifier les categories de services',
        'ajouter les categories de services',
        'supprimer une categorie de services',
        "se deconnecter",
      ],

      'employeur' => [
        "s'inscrire",
        'se connecter',
        'publier des annonces',
        'accepter une candidature',
        'rejeter une candidature',
        'voir la liste des candidatures',
        'voir la liste des candidats au niveau de la plateforme',
        'donner des recommandations',
        'evaluer les anciens employes',
        "se deconnecter",
      ],

      'demandeur_d_emploi'=> [
        "s'inscrire",
        "se connecter",
        "rechercher des offres d'emploi",
        "consulter une offre d'emploi",
        "postuler à une offre d'emploi",
        "enregistrer les offres d'emplois",
        "se deconnecter"
      ],
   ];
   foreach ($roles as $roleName => $permissions) {
    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);

    foreach ($permissions as $permission) {
        $role->givePermissionTo(Permission::firstWhere(['name' => $permission, 'guard_name' => 'api']));
    }
}

    }
}

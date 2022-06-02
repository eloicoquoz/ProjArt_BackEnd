<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classe;

class DatabaseSeeder extends Seeder
{

    
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         //\App\Models\User::factory(10)->create();
        DB::table('destinataires')->delete();
        DB::table('notifications')->delete();
        DB::table('classe_cours')->delete();
        DB::table('classe_user')->delete();
        DB::table('cours_user')->delete();
        DB::table('matiere_user')->delete();
        DB::table('remarques')->delete();
        DB::table('events')->delete();
        DB::table('role_user')->delete();
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('cours_salle')->delete();
        DB::table('cours')->delete();
        DB::table('classes')->delete();
        DB::table('departements')->delete();
        DB::table('salles')->delete();
        DB::table('matieres')->delete();

        DB::table('destinataires')->truncate();
        DB::table('notifications')->truncate();
        DB::table('classe_cours')->truncate();
        DB::table('classe_user')->truncate();
        DB::table('cours_user')->truncate();
        DB::table('matiere_user')->truncate();
        DB::table('remarques')->truncate();
        DB::table('events')->truncate();
        DB::table('role_user')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();
        DB::table('cours_salle')->truncate();
        DB::table('cours')->truncate();
        DB::table('classes')->truncate();
        DB::table('departements')->truncate();
        DB::table('salles')->truncate();
        DB::table('matieres')->truncate();
          
        
        DB::table('events')->insert([
            'Titre' => 'Bal de fin',
            'Debut' => '2022-06-01 18:00:00',
            'Fin' => '2022-06-01 23:00:00',
            'Description' => 'Bal de fin, organisé par l\'AGE',
            'Lieu' => 'Melon coton',
            'user_Email' => 'alexia.leger@heig-vd.ch',
        ]);

        DB::table('remarques')->insert([
           'Titre' => 'Examen',
            'Description' => 'Examen de fin d\'année',
            'Visibilite'  => 'public',
            'Date' => '2022-06-01',
            'user_Email' => 'lucas.cuennet@heig-vd.ch',
            'cours_id' => 1,
        ]);

        DB::table('remarques')->insert([
            'Titre' => 'Rapport',
             'Description' => 'Rapport de projet',
             'Visibilite'  => 'prive',
             'Date' => '2022-06-01',
             'user_Email' => 'alexia.leger@heig-vd.ch',
             'cours_id' => 3,
         ]);

         DB::table('notifications')->insert([
            'Objet' => 'Rapport de projet',
            'Message' => 'Vous avez un nouveau rapport de projet',
            'EnvoiHeureDate' => '2022-06-01 18:00:00',
            'user_Email' => 'eloi.coquoz@heig-vd.ch',
         ]);

         DB::table('destinataires')->insert([
            'notification_id' => 1,
            'user_Email' => 'lucas.cuennet@heig-vd.ch',
         ]);

         DB::table('destinataires')->insert([
            'notification_id' => 1,
            'user_Email' => 'alexia.leger@heig-vd.ch',
         ]);
    
    
    }
}

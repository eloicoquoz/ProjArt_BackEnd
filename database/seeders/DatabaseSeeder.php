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

        DB::table('departements')->insert([
            'id' => 'COMEM+',
        ]);
        DB::table('roles')->insert([
            'id' => 'Etudiant',
        ]);
        DB::table('roles')->insert([
            'id' => 'Professeur',
        ]);
        DB::table('roles')->insert([
            'id' => 'Administration',
        ]);
        DB::table('roles')->insert([
            'id' => 'AGE',
        ]);
        DB::table('users')->insert([
            
            'FullName' => 'Eloi Coquoz',
            'Email' => 'eloi.coquoz@heig-vd.ch',
            'Password' => Hash::make('coquoz'),
        ]);
        DB::table('users')->insert([
            
            'FullName' => 'Lucas Cuennet',
            'Email' => 'lucas.cuennet@heig-vd.ch',
            'Password' => Hash::make('cuennet'),
        ]);
        DB::table('users')->insert([
            
            'FullName' => 'Stephane Sordet',
            'Email' => 'stephane.sordet@heig-vd.ch',
            'Password' => Hash::make('sordet'),
        ]);
        DB::table('users')->insert([
            
            'FullName' => 'Alexia Leger',
            'Email' => 'alexia.leger@heig-vd.ch',
            'Password' => Hash::make('leger'),
        ]);
        DB::table('role_user')->insert([
            'user_Email' => 'eloi.coquoz@heig-vd.ch',
            'role_id' => 'Administration',
        ]);
        DB::table('role_user')->insert([
            'user_Email' => 'lucas.cuennet@heig-vd.ch',
            'role_id' => 'Etudiant',	
        ]);
        DB::table('role_user')->insert([
            'user_Email' => 'stephane.sordet@heig-vd.ch',
            'role_id' => 'Professeur',
        ]);
        DB::table('role_user')->insert([
            'user_Email' => 'alexia.leger@heig-vd.ch',
            'role_id' => 'AGE',
        ]);
        DB::table('role_user')->insert([
            'user_Email' => 'alexia.leger@heig-vd.ch',
            'role_id' => 'Etudiant',
        ]);

        $response = file_get_contents('https://chabloz.eu/files/horaires/all.json');
        $response = json_decode($response);
        $liste = array();
        foreach($response as $mydata)
        {
            $classe = $mydata->class;
            if(!in_array($classe, $liste, true)){
                array_push($liste, $classe);
            }
        }
        foreach ($liste as &$value) {
            DB::table('classes')->insert([
                'id' => $value,
                'departement_id' => 'COMEM',
            ]);
        } 

        $salle = array();
        foreach($response as $mydata)
        {
            $roomExplode = explode (",", $mydata->room);
            $trimmed_myArray = array_map('trim',$roomExplode);
            foreach($trimmed_myArray as $room) {
                array_push($salle, $room);
            }
        }
        $salleUnique = array_filter(array_unique($salle));
        foreach ($salleUnique as &$value) {
            DB::table('salles')->insert([
                'id' => $value,
            ]);
        } 

        foreach($response as $mydata)
        {
                $start = date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($mydata->start)));
                $end = date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($mydata->end)));
                DB::table('cours')->insert([
                    'Debut' => $start,
                    'Fin' => $end,
                    'matiere_id' => $mydata->label,
                ]);
        }

        $i = 1;
        foreach($response as $mydata)
        {
            $roomExplode = explode (",", $mydata->room);
            $trimmed_myArray = array_filter(array_map('trim',$roomExplode));
            foreach($trimmed_myArray as $room) {
            DB::table('cours_salle')->insert([
                'salle_id' => $room,
                'cours_id' => $i,
            ]);
            }
            $i++;
        }
          
        
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

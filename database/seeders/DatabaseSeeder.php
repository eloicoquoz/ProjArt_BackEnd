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
        DB::table('role_user')->delete();
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('cours')->delete();
        DB::table('classes')->delete();
        DB::table('filieres')->delete();
        DB::table('salles')->delete();
        DB::table('matieres')->delete();
        DB::table('filieres')->insert([
            'id' => 'COMEM',
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
            'Nom' => 'Coquoz',
            'Prenom' => 'Eloi',
            'Email' => 'eloi.coquoz@heig-vd.ch',
            'Password' => Hash::make('coquoz'),
        ]);
        DB::table('users')->insert([
            'Nom' => 'Cuennet',
            'Prenom' => 'Lucas',
            'Email' => 'lucas.cuennet@heig-vd.ch',
            'Password' => Hash::make('cuennet'),
        ]);
        DB::table('users')->insert([
            'Nom' => 'Sordet',
            'Prenom' => 'Stephane',
            'Email' => 'stephane.sordet@heig-vd.ch',
            'Password' => Hash::make('sordet'),
        ]);
        DB::table('users')->insert([
            'Nom' => 'Leger',
            'Prenom' => 'Alexia',
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
                'filiere_id' => 'COMEM',
            ]);
        } 

        $matiere = array('MarkDig1', 'Ang1', 'MedSerGam', 'ProtoEnv', 'DocWeb', 'BaseProg1', 'MéthOut', 'BaseMath1', 'FondMedias', 'ComHum', 'MarDévProd', 'DeDonAInf1', 'RechAnPub', 'EvolMétMéd', 'EcrireWeb', 'Ang2', 'GesBudget', 'BaseProg2', 'ComVisuel', 'ProdConMé1', 'BaseMath2', 'ProgServ1', 'ProgServ2', 'MarkDig2', 'InfraDon1', 'DeDonAInf2', 'AnalysMar', 'PilotFin', 'ProgWeb', 'EcoPrint', 'WebDon', 'ProdConMé2', 'InfraDonn2', 'ArchiDép', 'MétRecher', 'Ecomm', 'Socio', 'StratMarq', 'Droit1', 'TechAv', 'DévProdMéd', 'WebMobUI', 'PropVal', 'ConceptUI', 'UXDesign', 'VenteProj', 'LabVeilSoc', 'VisualDon', 'Droit2', 'ProjArt', 'ArchInfoUX', 'ArchiOWeb', 'BusPlan', 'DévMobil', 'EvalOptPro', 'LabVeilTec', 'ProfilPro', 'Startup', 'SysComplex', 'UXLab', 'ApproMédia', 'CRUNCH', 'OPTIOT', 'OPTTMARK', 'OPTVR', 'ProjInt');
        foreach ($matiere as &$value) {
            DB::table('matieres')->insert([
                'id' => $value,
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
            DB::table('salle_cours')->insert([
                'salle_id' => $room,
                'cours_id' => $i,
                'Debut' => $start,
                'Fin' => $end,
            ]);
            }
            $i++;
        }
            
    
    }
}

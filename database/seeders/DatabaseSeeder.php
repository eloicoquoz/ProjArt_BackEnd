<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Classe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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


        $textCnt  = "./resources/Professeurs.txt";
        $contents = file_get_contents($textCnt);
        $arrfields = explode(',', $contents);
        foreach($arrfields as $field) {
            $arr = explode(' ', $field);
            $pseudo = $arr[0];
            $nom = $this->fctRetirerAccents(strtolower($arr[1]));
            $prenom = $this->fctRetirerAccents(strtolower($arr[2]));
            $email = $prenom . '.' . $nom . '@heig-vd.ch';
            $nomPrenom = $arr[1] . ' ' . $arr[2];
            $user = User::where('Email', $email)->first();
            if (!$user) {
                $user = new User();
                $user->Email = $email;
                $user->FullName = $nomPrenom;
                $user->Acronyme = $pseudo;
                $user->save();
            }
            $role = Role::where('id', 'Professeur')->first();
                if (!$role) {
                    $role = new Role();
                    $role->id = 'Professeur';
                    $role->save();
                }
            $user->roles()->attach('Professeur');                
        }

        $textCnt1  = "./resources/Administration.txt";
        $contents1 = file_get_contents($textCnt1);
        $arrfields1 = explode(',', $contents1);
        foreach($arrfields1 as $field1) {
            $arr1 = explode(' ', $field1);
            $pseudo1 = $arr1[0];
            $nom1 = $this->fctRetirerAccents(strtolower($arr1[1]));
            $prenom1 = $this->fctRetirerAccents(strtolower($arr1[2]));
            $email1 = $prenom1 . '.' . $nom1 . '@heig-vd.ch';
            $nomPrenom1 = $arr1[1] . ' ' . $arr1[2];
            $user1 = User::where('Email', $email1)->first();
            if (!$user1) {
                $user1 = new User();
                $user1->Email = $email1;
                $user1->FullName = $nomPrenom1;
                $user1->Acronyme = $pseudo1;
                $user1->save();
            }
            $role1 = Role::where('id', 'Administration')->first();
                if (!$role1) {
                    $role1 = new Role();
                    $role1->id = 'Administration';
                    $role1->save();
                }
            $user1->roles()->attach('Administration');                
        }
        $this->scrapeForClasses();
    
    }

    function fctRetirerAccents($varMaChaine)
		{
			$search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
			//Préférez str_replace à strtr car strtr travaille directement sur les octets, ce qui pose problème en UTF-8
			$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');

			$varMaChaine = str_replace($search, $replace, $varMaChaine);
			return $varMaChaine; //On retourne le résultat
		}
    
    function scrapeForClasses()
    { /* IN ORDER TO USE, FILL THE EMAIL AND PASSWORD USED TO CONNECT TO GAPS */
        $email = 'eloi.coquoz@heig-vd.ch';
        $pwd = '5g@19bE#6CX)';
        $textCnt  = "./resources/Classes.txt";
        $contents = file_get_contents($textCnt);
        $arrfields = explode(',', $contents);
        foreach($arrfields as $field){
            $arr = explode('_', $field);
            $department = $arr[0];
            $class = $arr[1];
            app('App\Http\Controllers\ScrapingController')->getTimetablesForClass($email, $pwd, $class, $department);
        }
    }
    
    
}
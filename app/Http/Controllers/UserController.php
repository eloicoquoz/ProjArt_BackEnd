<?php

namespace App\Http\Controllers;

use DOMXPath;
use Throwable;
use DOMDocument;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Exists;
use Symfony\Component\CssSelector\XPath\XPathExpr;
use Illuminate\Support\Str;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $users;
    }

    /**
     * Voir les professeurs en fonction d'un cours
     *
     * @return listes des professeurs
     */
    public function ProfByCours($cours)
    {
        return DB::table('users')
            ->join('cours_user', 'users.Email', '=', 'cours_user.user_Email')
            ->where('cours_user.cours_id', $cours, 1)
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('role_user.role_id', 'Professeur', 1)
            ->select('users.FullName', 'users.Email')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($email, $pwd, $fullName)
    {
        $user = User::where('Email', $email)->first();
        if (!$user) {
            $user = new User();
            $user->Email = $email;
            $user->Password = $pwd;
            $user->FullName = $fullName;
            $user->save();
        }else{
            $user->Password = $pwd;
            $user->save();
        }
        $this->testRole($email, $fullName);
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $user = User::where('Email', $email)->first();
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Fonction de login, recherche l'utilisateur dans la base de données. Si l'utilisateur existe au sein de la DB, on teste si le password correspond.
     * Si l'utilisateur n'est pas dans la DB, on appelle la fonction signup pour créer un nouvel utilisateur.
     *
     * @return message
     */
    public function login(Request $request)
    {
        $email = $request->input('Email');
        $password = $request->input('Password');
        if (User::where('Email', '=', $email)->exists()) {
            $user = User::where('Email', '=', $email)->first();
            if (Hash::check($password, $user->Password)) {
                echo ('user found and connected,'.$user->roles()->get());
            } elseif($user->Password == null) {
                self::signup($password, $email);
            }
            else {
                echo ('user found : error in password or username');
            }
        } else {
            self::signup($password, $email);
        }
    }


    /**
     * Fonction de signup, crée un nouvel utilisateur dans la DB si l'utilisateur n'existe pas déjà. Cependant l''utilisateur doit posséder un compte sur GAPS avec les identifiants fournis.
     * On teste sur GAPS avec les identifiants et on scrappe les données de la page retournée. Selon les données reçues, on crée un nouvel utilisateur dans la DB via la méthode storeUser.
     *
     * @return message
     */
    public function signup($password, $email)
    {
        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login=' . urlencode($email) . '&password=' . urlencode($password) . '&submit=Entrer';
        $response = Http::get($url);
        $dom = new DOMDocument();
        @$dom->loadHTML($response->body());
        $xpath = new DOMXPath($dom);
        $nomPrenom = $xpath->query('/html/body/div[1]/div[6]/h3/a');

        if ($nomPrenom->length == 0) {
            echo ('user not found on gaps, error in email or password');
        } else {
            $nomEntier = $nomPrenom->item(0)->nodeValue;

            echo ("User found on gaps and will be stored in DB");

            self::store($email, $password, $nomEntier);
            app('App\Http\Controllers\ScrapingController')->getPersonalTimetable($email, $password, $nomPrenom);
        }
    }

    public function testRole($email, $nomEntier){
        $textCnt  = "../resources/Professeurs.txt";
        $contents = file_get_contents($textCnt);
        $arrfields = explode(',', $contents);
        $role = 'Etudiant';
        foreach($arrfields as $field) {
            $arr = explode(' ', $field);
            $nom = $arr[1];
            $prenom = $arr[2];
            $nomPrenom = $nom . ' ' . $prenom;
            if ($nomPrenom == $nomEntier) {
                $role = 'Professeur';
            }
        }
        echo nl2br("\n role, " . $role);
        $this->addRoleToUser($role, $email);
    }

    /**
     * Fonction de création d'un role pour un utilisateur.
     *
     */
    public function addRoleToUser($roles, $email)
    {
        $user = User::where('Email', $email)->first();
        if ($user) {
            $role = Role::where('id', $roles)->first();
            if (!$role) {
                $role = new Role();
                $role->id = $roles;
                $role->save();
            }
            $hasProf = $user->roles()->where('id', $roles)->first();
            if (!$hasProf) {
                $user->roles()->attach($roles);
            }
            echo nl2br("\n role added to user");
        }
    }


    /**
     * Fonction de réinitialisation de mot de passe.
     *
     */
    public function oubliMdp($email)
    {

        $motdepasse = Str::random(15);
        $from = 'eloi.coquoz@bluewin.ch';
        // Message
        $message = "Bonjour,\r\nVoici le nouveau mot de passe pour l'application XXX : \r\n" . $motdepasse;
        // Dans le cas où nos lignes comportent plus de 70 caractères, nous les coupons en utilisant wordwrap()
        $message = wordwrap($message, 70, "\r\n");
        // Header
        $headers = "From:" . $from;
        $user = User::where('Email', '=', $email)->exists()->first();

        if ($user) {
            // Envoi du mail
            $retval = mail($email, 'Réinitialisation du mot de passe', $message, $headers);
            if ($retval == true) {
                echo "Message sent successfully...";
                $user->Password = $motdepasse;
            } else {
                echo "Message could not be sent...";
            }
        }else{
            echo "user not found";
        }
    }
        
}

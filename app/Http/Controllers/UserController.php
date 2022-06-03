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
use Illuminate\Validation\Rules\Exists;
use Symfony\Component\CssSelector\XPath\XPathExpr;

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
            $user->Password = Hash::make($pwd);
            $user->FullName = $fullName;
            $user->save();
        }
        $this->addRoleToUser('Etudiant', $email);
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

    public function login($password, $email)
    {

        if (User::where('Email', '=', $email)->exists()) {
            $user = User::where('Email', '=', $email)->first();
            if (Hash::check($password, $user->Password)) {
                echo ('user found and connected');
            } else {
                echo ('user found : error in password or username');
            }
        } else {
            self::signup($password, $email);
        }
    }


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

            self::storeUser($email, $password, $nomEntier);
            app('App\Http\Controllers\ScrapingController')->getPersonalTimetable($email, $password, $nomPrenom);
        }
    }



    public function storeUser($email, $password, $nomEntier)
    {
        $etudiant = Role::where('id', 'Etudiant')->first();
        if (!$etudiant) {
            $etudiant = new Role();
            $etudiant->id = 'Etudiant';
            $etudiant->save();
        }

        DB::table('users')->insert([
            'FullName' => $nomEntier,
            'Email' => $email,
            'Password' => Hash::make($password),
        ]);

        DB::table('role_user')->insert([
            'user_Email' => $email,
            'role_id' => 'Etudiant',
        ]);
        echo nl2br("\n stored in DB");
    }

    public function addRoleToUser($role, $email){
        $user = User::where('Email', $email)->first();
        if ($user) {
            $role = Role::where('id', $role)->first();
            if (!$role) {
                $role = new Role();
                $role->id = $role;
                $role->save();
            }
            $user->roles()->attach($role);
        }
    }
}

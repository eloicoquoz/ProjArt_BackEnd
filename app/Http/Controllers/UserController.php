<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;
use Throwable;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;
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
            ->join('user_cours', 'users.Email', '=', 'user_cours.user_Email')
            ->where('user_cours.cours_id', $cours, 1)
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('role_user.role_id', 'Professeur', 1)
            ->select('users.Nom', 'users.Prenom', 'users.Email')
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        if (User::where('email', '=', $email)->exists()) {
            $user = User::where('email', '=', $email)->first();
            if (Hash::check($password, $user->Password)) {
                echo ('user found and connected');
            } else {
                echo ('user not found : error in password or username');
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

            echo ('user found on gaps and will be stored in DB');

            self::storeUser($email, $password, $nomEntier);
        }
    }



    public function storeUser($email, $password, $nomEntier)
    {
        DB::table('users')->insert([
            'FullName' => $nomEntier,
            'Email' => $email,
            'Password' => Hash::make($password),
        ]);

        DB::table('role_user')->insert([
            'user_Email' => $email,
            'role_id' => 'Etudiant',
        ]);
        echo ('user  found on gaps and stored in DB');
    }
}

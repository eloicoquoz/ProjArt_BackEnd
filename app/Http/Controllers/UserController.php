<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;
use Throwable;


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
        }else{
            echo ('account is not created');
        }
    }


    public function signup($password, $email, $prenom, $nom)
    {


        if (User::where('email', '=', $email)->exists()) {
            echo ('user already exists, please log in');
        } else {
            echo ('user not found, to check on gaps');
            $path = "https://gaps.heig-vd.ch/consultation/elections/candidatures.php?login=" . $email . "&password=" . $password . "&submit=Entrer";
            $ch = curl_init();
            try {
                curl_setopt($ch, CURLOPT_URL, $path);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                $fauxlogin = "class=\"fauxLogin\"";
                $notLog = str_contains($response, $fauxlogin);
            } catch (Throwable $th) {
                throw $th;
            } finally {
                curl_close($ch);
            }

            if ($notLog) {
                echo ('user not found on gaps, error in email or password');
            } else {
                DB::table('users')->insert([
                    'Nom' => $nom,
                    'Prenom' => $prenom,
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
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;
use Illuminate\Support\Facades\DB;

class CoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function allCours()
    {
        return $cours=Cours::orderBy('Debut', 'asc')->get();
    }
    

    public function allCoursInfo()
    {
        return DB::table('cours')
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->leftJoin('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('role_user.role_id', 'Professeur', 1)
        ->orWhere('role_user.role_id', null, 1)
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom', 'role_user.role_id')
        ->orderBy('cours.Debut', 'asc')
        ->get();
    }

    public function CoursById($id)
    {
        return DB::table('cours')
        ->where('id', $id, 1)
        ->get();
    }

    public function CoursByIdInfo($id)
    {    
        $query1 = DB::table('cours')
        ->where('cours.id', $id, 1)
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->leftJoin('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('role_user.role_id', 'Professeur', 1)
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom', 'role_user.role_id');

        $query2 = DB::table('cours')
        ->where('cours.id', $id, 1)
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->leftJoin('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('role_user.role_id', null, 1)
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom', 'role_user.role_id')
        ->union($query1)
        ->get();

        $result = $query2;

        return $result;
    }

    public function CoursByClasse($classe)
    {
        return DB::table('cours')
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->join('cours_classe', 'cours.id', '=', 'cours_classe.cours_id')
        ->leftJoin('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('cours_classe.classe_id', $classe, 1)
        ->where('role_user.role_id', 'Professeur', 1)
        ->orderBy('cours.Debut', 'asc')
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom', 'role_user.role_id')
        ->get();
    }

    public function CoursByClasseByMatiere($classe, $matiere)
    {
        return DB::table('cours')
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->join('cours_classe', 'cours.id', '=', 'cours_classe.cours_id')
        ->leftJoin('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('cours_classe.classe_id', $classe, 1)
        ->where('role_user.role_id', 'Professeur', 1)
        ->where('cours.matiere_id', $matiere, 1)
        ->orderBy('cours.Debut', 'asc')
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom', 'role_user.role_id')
        ->get();
    }

    public function CoursByUser($user)
    {
        return DB::table('cours')
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->join('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->where('users.Email', $user, 1)
        ->orderBy('cours.Debut', 'asc')
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom')
        ->get();
    }

    public function CoursByUserByMatiere($user, $matiere)
    {
        return DB::table('cours')
        ->leftJoin('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->join('user_cours', 'cours.id', '=', 'user_cours.cours_id')
        ->leftJoin('users', 'users.Email', '=', 'user_cours.user_Email')
        ->where('users.Email', $user, 1)
        ->where('cours.matiere_id', $matiere, 1)
        ->orderBy('cours.Debut', 'asc')
        ->select('cours.*', 'salle_cours.salle_id', 'users.Nom', 'users.Prenom')
        ->get();
    }


    public function SalleByCours()
    {
        return DB::table('salle_cours')->get();
    }

    public function CoursByMatiere($matiere)
    {
        return DB::table('cours')->where('matiere_id', $matiere, 1)->get();
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
}

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


    /**
     * Voir tous les cours
     *
     * @return liste de tous les cours
     */
    public function allCours()
    {
        return $cours = Cours::orderBy('Debut', 'asc')->get();
    }


    /**
     * Voir toutes les informations de tous les cours (Salle, Matière, Professeur)
     *
     * @return liste de tous les cours avec informations complémentaires
     */
    public function allCoursInfo()
    {
        return DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->leftJoin('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('role_user.role_id', 'Professeur', 1)
            ->orWhere('role_user.role_id', null, 1)
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName', 'role_user.role_id')
            ->orderBy('cours.Debut', 'asc')
            ->get();
    }

    /**
     * Voir un cours en fonction de l'id
     *
     * @return le cours
     */
    public function CoursById($id)
    {
        return DB::table('cours')
            ->where('id', $id, 1)
            ->get();
    }

    /**
     * Voir toutes les informations d'un cours en fonction de l'id (Salle, Matière, Professeur)
     *
     * @return le cours avec informations complémentaires
     */
    public function CoursByIdInfo($id)
    {
        $query1 = DB::table('cours')
            ->where('cours.id', $id, 1)
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->leftJoin('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('role_user.role_id', 'Professeur', 1)
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName', 'role_user.role_id');

        $query2 = DB::table('cours')
            ->where('cours.id', $id, 1)
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->leftJoin('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('role_user.role_id', null, 1)
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName', 'role_user.role_id')
            ->union($query1)
            ->get();

        $result = $query2;

        return $result;
    }

    /**
     * Voir toutes les informations des cours en fonction d'une classe (Salle, Matière, Professeur)
     *
     * @return liste des cours par classes avec informations complémentaires
     */
    public function CoursByClasse($classe)
    {
        return DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->join('classe_cours', 'cours.id', '=', 'classe_cours.cours_id')
            ->leftJoin('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('classe_cours.classe_id', $classe, 1)
            //->where('role_user.role_id', 'Professeur', 1)
            ->orderBy('cours.Debut', 'asc')
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName', 'role_user.role_id')
            ->distinct()
            ->get();
    }

    /**
     * Voir toutes les informations des cours en fonction d'une classe et d'une matière (Salle, Matière, Professeur)
     *
     * @return liste des cours par classes et par matiere avec informations complémentaires
     */
    public function CoursByClasseByMatiere($classe, $matiere)
    {
        return DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->join('classe_cours', 'cours.id', '=', 'classe_cours.cours_id')
            ->leftJoin('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('classe_cours.classe_id', $classe, 1)
            // ->where('role_user.role_id', 'Professeur', 1)
            ->where('cours.matiere_id', $matiere, 1)
            ->orderBy('cours.Debut', 'asc')
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName', 'role_user.role_id')
            ->get();
    }

    /**
     * Voir toutes les informations des cours en fonction d'un user (Salle, Matière, Professeur)
     *
     * @return liste des cours par user avec informations complémentaires
     */
    public function CoursByUser($user)
    {
        return DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->where('users.Email', $user, 1)
            ->orderBy('cours.Debut', 'asc')
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName')
            ->get();
    }

    /**
     * Voir toutes les informations des cours en fonction d'un user et d'une matière (Salle, Matière, User)
     *
     * @return liste des cours par user et par matiere avec informations complémentaires
     */
    public function CoursByUserByMatiere($user, $matiere)
    {
        return DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->where('users.Email', $user, 1)
            ->where('cours.matiere_id', $matiere, 1)
            ->orderBy('cours.Debut', 'asc')
            ->select('cours.*', 'cours_salle.salle_id', 'users.FullName')
            ->get();
    }

    /**
     * Voir les professeur en fonction d'un user et d'une matière
     *
     * @return liste des professeurs par user et par matiere
     */
    public function ProfByUserByMatiere($user, $matiere)
    {
        $allCours = DB::table('cours')
            ->leftJoin('cours_salle', 'cours.id', '=', 'cours_salle.cours_id')
            ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->leftJoin('users', 'users.Email', '=', 'cours_user.user_Email')
            ->where('users.Email', $user, 1)
            ->where('cours.matiere_id', $matiere, 1)
            ->orderBy('cours.Debut', 'asc')
            ->select('cours.id')
            ->get();

        $response = json_decode($allCours);
        foreach ($response as $mydata) {
            return self::CoursByIdInfo($mydata->id);
        }
    }


    /**
     * Voir la liste de toutes les salles utilisées lors de cours
     *
     * @return liste des salles utilisées
     */
    public function SalleByCours()
    {
        return DB::table('cours_salle')->get();
    }

    /**
     * Voir la liste des cours en fonction d'une matière
     *
     * @return liste des cours en fonction d'une matière
     */
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
        $cours = new Cours();
        $cours->Debut = $request->Debut;
        $cours->Fin = $request->Fin;
        $cours->matiere_id = $request->matiere_id;
        $cours->save();
    }

    public function storeScrapping()
    {

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
        $cours = Cours::findOrFail($id);
        $cours->Debut = $request->Debut;
        $cours->Fin = $request->Fin;
        $cours->matiere_id = $request->matiere_id;
        $cours->save();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cours = Cours::findOrFail($id);
        $cours->users()->delete();
        $cours->salles()->delete();
        $cours->classes()->delete();
        $cours->remarque()->delete();
        $cours->delete();
        return redirect()->back();
    }
}

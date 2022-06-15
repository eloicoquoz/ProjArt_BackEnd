<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cours;
use App\Models\Salle;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Notification;
use Illuminate\Http\Request;
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
            ->where('role_user.role_id', 'Professeur', 1)
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
            ->where('role_user.role_id', 'Professeur', 1)
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

    public function ProfByFiliere($filiere)
    {
        return DB::table('cours')
            ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->join('users', 'users.Email', '=', 'cours_user.user_Email')
            ->join('classe_cours', 'cours.id', '=', 'classe_cours.cours_id')
            ->leftJoin('classes', 'classes.id', '=', 'classe_cours.classe_id')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('classes.departement_id', $filiere, 1)
            ->where('role_user.role_id', 'Professeur', 1)
            ->select('users.*')
            ->distinct()
            ->get();
    }

    public function EtudiantbyClasse($classe)
    {
        return DB::table('cours')
            ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
            ->join('users', 'users.Email', '=', 'cours_user.user_Email')
            ->join('classe_cours', 'cours.id', '=', 'classe_cours.cours_id')
            ->leftJoin('classes', 'classes.id', '=', 'classe_cours.classe_id')
            ->leftJoin('role_user', 'users.Email', '=', 'role_user.user_Email')
            ->where('classes.id', $classe, 1)
            ->where('role_user.role_id', 'Etudiant', 1)
            ->select('users.*')
            ->distinct()
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
        $matiere = Matiere::where('id', $request->Matiere)->first();
        if ($matiere) {
            $cours = new Cours();
            $cours->Debut = $request->Debut;
            $cours->Fin = $request->Fin;
            $cours->matiere_id = $request->Matiere;
            $salles = $request->Salles;
            $salles = explode(' ', $salles);
            $cours->save();
            foreach ($salles as $salle) {
                $uneSalle = Salle::where('id', $salle)->first();
                if (!$uneSalle){
                    $uneSalle = new Salle();
                    $uneSalle->id = $salle;
                    $uneSalle->save();
                }
            }
            $cours->salles()->sync($salles);
            $classes = $request->Classes;
            $classes = explode(' ', $classes);
            $usersInCourse = array();
            foreach ($classes as $class) {
                $classe = Classe::where('id', $class)->first();
                $classe->cours()->attach($cours->id);
                $usersInClass = $classe->users()->get();
                foreach ($usersInClass as $user) {
                    $theUser = $user->matieres()->where('id', $cours->matiere_id)->first();
                    if ($theUser) {
                        $usersInCourse[] = $user->Email;
                    }
                }
            }
            $usersInCourse = array_unique($usersInCourse);
            foreach ($usersInCourse as $user) {
                $cours->users()->attach($user);
            }
            $prof = $request->Prof;
            $profUser = User::where('Acronyme', $prof)->first();
            if ($profUser) {
                $cours->users()->attach($profUser->Email);
            }
            $user = $request->User;
            $notification = app('App\Http\Controllers\NotificationController')->store('Nouveau cours ajouté', 'Un cours de ' . $cours->matiere_id . ' a été ajouté.', $user);
            $destinataires = app('App\Http\Controllers\DestinataireController')->notifyNewCours($usersInCourse, $notification->id);
            return response()->json(['success' => 'Cours ajouté avec succès', 'notification' => $notification->id]);
        } else {
            return response()->json(['error' => 'Matière non existante']);
        }
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
        $salles = $request->Salles;
        $salles = explode(' ', $salles);
        $cours->save();
        $cours->salles()->sync($salles);
        $titre = "Modification d'un cours";
        $desc = "Le cours de " . $cours->matiere_id . " du " . $cours->Debut . " a été modifié.";
        $user = $request->User;
        $notification = app('App\Http\Controllers\NotificationController')->store($titre, $desc, $user);
        $destinataire = app('App\Http\Controllers\DestinataireController')->store($cours->id, $notification->id);
        return response()->json(['success' => 'Cours modifié avec succès']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $cours = Cours::findOrFail($id);
        $titre = "Cours supprimé";
        $notification = app('App\Http\Controllers\NotificationController')->store($titre, $titre, $request->User);
        $destinataire = app('App\Http\Controllers\DestinataireController')->store($id, $notification->id);
        $cours->users()->detach();
        $cours->salles()->detach();
        $cours->classes()->detach();
        $cours->remarques()->delete();
        $cours->delete();
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remarque;
use App\Models\User;
use App\Models\Cours;
use App\Models\Matiere;
use Illuminate\Support\Facades\DB;


class RemarqueController extends Controller
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

    public function allRemarque()
    {
        return Remarque::orderBy('Date', 'asc')->get();
    }

    /**
     * Voir un remarque par son id
     *
     * @return la remarque correspondante
     */
    public function RemarqueById($id)
    {
        return Remarque::where('id', $id)->get();
    }

    /**
     * Voir toutes les remarques publiques ainsi que les remarques privés d'un utilisateur
     *
     * @return toutes remarques publiques et remarques privées d'un utilisateur
     */
    public function RemarqueByUser($user)
    {
        $query1 = DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->where('remarques.Visibilite', 'public', 1)
        ->select('remarques.*', 'cours.matiere_id');

        $query2 = DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
        ->where('remarques.user_Email', $user, 1)
        ->where('remarques.Visibilite', 'prive', 1)
        ->select('remarques.*', 'cours.matiere_id')
        ->union($query1)
        ->orderBy('Date', 'asc')
        ->get();

        $result = $query2;

        return $result;
    }

    /**
     * Voir toutes les remarques publiques ainsi que les remarques privés d'un utilisateur en fonction d'une matière
     *
     * @return toutes remarques publiques et remarques privées d'un utilisateur en fonction de la matière
     */
    public function RemarqueByUserByMatiere($user,$matiere)
    {

        $query1 = DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->where('cours.matiere_id', $matiere, 1)
        ->where('remarques.Visibilite', 'public', 1)
        ->select('remarques.*');

        $query2 = DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->join('cours_user', 'cours.id', '=', 'cours_user.cours_id')
        ->where('cours.matiere_id', $matiere, 1)
        ->where('remarques.user_Email', $user, 1)
        ->where('remarques.Visibilite', 'prive', 1)
        ->select('remarques.*')
        ->union($query1)
        ->orderBy('Date', 'asc')
        ->get();

        $result = $query2;

        return $result;
    }


    /**
     * Voir toutes les remarques publiques d'une classe en en fonction d'une matière
     *
     * @return toutes remarques publiques de la classe en fonction de la matière
     */
    public function RemarqueByClasseByMatiere($classe,$matiere)
    {
        return DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->join('classe_cours', 'cours.id', '=', 'classe_cours.cours_id')
        ->where('cours.matiere_id', $matiere, 1)
        ->where('classe_cours.classe_id', $classe, 1)
        ->where('remarques.Visibilite', 'public', 1)
        ->orderBy('remarques.Date', 'asc')
        ->select('remarques.*')
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
        $notification="";
        $user = User::where('email', $request->user_Email)->first();
        $cours = Cours::where('id', $request->cours_id)->first();
        if($user && $cours){
            $remarque = new Remarque();
            $remarque->Titre = $request->Titre;
            $remarque->Description = $request->Description;
            $remarque->Visibilite = $request->Visibilite;
            $remarque->Date = $request->Date;
            $remarque->user_Email = $request->user_Email;
            $remarque->cours_id = $request->cours_id;
            if($request->Visibilite == "public" && $request->Titre=="retard"){
                $titre = "Annonce de retard";
                $user = User::findOrFail($request->user_Email);
                $cours = Cours::findOrFail($request->cours_id);
                $matiere = Matiere::findOrFail($cours->matiere_id);
                $description = "Retard de " . $user->FullName . " pour le cours de " . $matiere->id. " du ".$cours->Debut;
                $notification = app('App\Http\Controllers\NotificationController')->store($titre, $description,$request->user_Email);
                app('App\Http\Controllers\DestinataireController')->store($request->cours_id, $notification->id);
            }elseif($request->Visibilite == "public"){
                $titre = "Nouvelle remarque";
                $user = User::findOrFail($request->user_Email);
                $cours = Cours::findOrFail($request->cours_id);
                $matiere = Matiere::findOrFail($cours->matiere_id);
                $description = "Nouvelle remarque de " . $user->FullName . " pour la matière suivante : " . $matiere->id;
                $notification = app('App\Http\Controllers\NotificationController')->store($titre, $description,$request->user_Email);
                app('App\Http\Controllers\DestinataireController')->store($request->cours_id, $notification->id);
            }
            $remarque->save();
            return response()->json(['success' => 'Remarque ajouté avec succès']);
        }
        else{
            return response()->json(['error' => 'Cours ou User non existante']);
        }
       
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
        $user = User::where('email', $request->user_Email)->first();
        $cours = Cours::where('id', $request->cours_id)->first();
        if($user && $cours){
            $remarque = Remarque::findOrFail($id);
            $remarque->Titre = $request->Titre;
            $remarque->Description = $request->Description;
            $remarque->Visibilite = $request->Visibilite;
            $remarque->Date = $request->Date;
            $remarque->user_Email = $request->user_Email;
            $remarque->cours_id = $request->cours_id;
            $remarque->save();
            return response()->json(['success' => 'Remarque ajouté avec succès']);
        }
        else{
            return response()->json(['error' => 'Cours ou User non existante']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $remarque = Remarque::findOrFail($id);
        $remarque->delete();
        return redirect()->back();
    }
}

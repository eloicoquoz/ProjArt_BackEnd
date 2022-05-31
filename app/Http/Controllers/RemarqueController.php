<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remarque;
use DB;

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

    public function RemarqueById($id)
    {
        return Remarque::where('id', $id)->get();
    }

    public function RemarqueByUser($user)
    {
        return Remarque::where('user_Email', $user)->orderBy('Date', 'asc')->get();
    }

    
    public function RemarqueByClasseByMatiere($classe,$matiere)
    {
        return DB::table('remarques')
        ->join('cours', 'remarques.cours_id', '=', 'cours.id')
        ->join('cours_classe', 'cours.id', '=', 'cours_classe.cours_id')
        ->where('cours.matiere_id', $matiere, 1)
        ->where('cours_classe.classe_id', $classe, 1)
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

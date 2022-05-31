<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;
use DB;

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
        ->join('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->select('cours.*', 'salle_cours.salle_id')
        ->get();
    }

    public function CoursById($id)
    {
        return DB::table('cours')->where('id', $id, 1)->get();
    }

    public function CoursByIdInfo($id)
    {
        return DB::table('cours')
        ->join('salle_cours', 'cours.id', '=', 'salle_cours.cours_id')
        ->where('cours.id', $id, 1)
        ->select('cours.*', 'salle_cours.salle_id')
        ->get();
    }

    public function CoursByClasse($classe)
    {
        return DB::table('cours_classe')->where('cours_id', $classe)->get();
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

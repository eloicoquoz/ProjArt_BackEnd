<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
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

    public function allEvent()
    {
        return Event::orderBy('Debut', 'asc')->get();
    }

    public function EventById($id)
    {
        return Event::where('id', $id)->get();
    }

    public function EventByUser($user)
    {
        return Event::where('user_Email', $user)->get();
    }

    public function EventByRole($role)
    {
        return DB::table('events')
        ->join('users', 'events.user_Email', '=', 'users.Email')
        ->join('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->where('role_user.role_id', $role, 1)
        ->orderBy('events.Debut', 'asc')
        ->select('events.*')
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
        $event = new Event();
        $event->Titre = $request->Titre;
        $event->Debut = $request->Debut;
        $event->Fin = $request->Fin;
        $event->Lieu = $request->Lieu;
        $event->user_Email = $request->user_Email;
        $event->Description = $request->Description;
        $titre = "Nouvel évènement";
        $notification = app('App\Http\Controllers\NotificationController')->store($titre, $request->Titre,$request->user_Email);
        $event->save();
    
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
        $event = Event::findOrFail($id);
        $event->Titre = $request->Titre;
        $event->Debut = $request->Debut;
        $event->Fin = $request->Fin;
        $event->Lieu = $request->Lieu;
        $event->user_Email = $request->user_Email;
        $event->Description = $request->Description;
        $event->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->back();
    }
}

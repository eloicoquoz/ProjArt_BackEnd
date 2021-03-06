<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EventRequest;

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

    /**
     * Voir tous les événements
     *
     * @return tous les événnements
     */
    public function allEvent()
    {
        return DB::table('events')
        ->join('users', 'events.user_Email', '=', 'users.Email')
        ->join('role_user', 'users.Email', '=', 'role_user.user_Email')
        ->orderBy('events.Debut', 'asc')
        ->where('role_user.role_id', 'AGE', 1)
        ->OrWhere('role_user.role_id', 'Administration', 1)
        ->select('events.*', 'users.FullName', 'role_user.role_id')
        ->distinct()
        ->get();
    }

    /**
     * Rechercher un événement dans la base de données via son id
     *
     * @param  int  $id
     * @return event
     */
    public function EventById($id)
    {
        return Event::where('id', $id)->get();
    }

    /**
     * Rechercher un événement dans la base de données via son user
     *
     * @param  int  $user
     * @return event
     */
    public function EventByUser($user)
    {
        return Event::where('user_Email', $user)->get();
    }

     /**
     * Rechercher un événement dans la base de données via son role
     *
     * @param  int  $role
     * @return event
     */
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
    public function store(EventRequest $request)
    {
        $user = User::where('Email', $request->user_Email)->first();
        if ($user) {
            $event = new Event();
            $event->Titre = $request->Titre;
            $event->Debut = $request->Debut;
            $event->Fin = $request->Fin;
            $event->Lieu = $request->Lieu;
            $event->user_Email = $request->user_Email;
            $event->Description = $request->Description;
            $titre = "Nouvel évènement";
            $notification = app('App\Http\Controllers\NotificationController')->store($titre, $request->Titre,$request->user_Email);
            $destinataire = app('App\Http\Controllers\DestinataireController')->notifyAll($notification->id);
            $event->save();
            return $event;
        }else{
            return response()->json(['error' => 'User not found'], 404);
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
    public function update(EventRequest $request, $id)
    {
        $user = User::where('Email', $request->user_Email)->first();
        if ($user) {
            $event = Event::findOrFail($id);
            $event->Titre = $request->Titre;
            $event->Debut = $request->Debut;
            $event->Fin = $request->Fin;
            $event->Lieu = $request->Lieu;
            $event->user_Email = $request->user_Email;
            $event->Description = $request->Description;
            $titre = "Évènement modifié";
            $notification = app('App\Http\Controllers\NotificationController')->store($titre, $request->Titre, $request->user_Email);
            $destinataire = app('App\Http\Controllers\DestinataireController')->notifyAll($notification->id);
            $event->save();
            return $event;
        }else{
            return response()->json(['error' => 'User not found'], 404);
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
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->back();
    }
}

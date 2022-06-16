<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cours;
use App\Models\Destinataire;
use Illuminate\Http\Request;
use App\Http\Requests\DestinataireRequest;

class DestinataireController extends Controller
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
    public function store($coursId, $notificationId)
    {
        $users = Cours::where('id', $coursId)->first()->users;
        foreach ($users as $user) {
            $destinataire = new Destinataire();
            $destinataire->user_Email = $user->Email;
            $destinataire->notification_id = $notificationId;
            $destinataire->Lu = false;
            $destinataire->save();
        }
    }

    public function notifyAll($notificationId)
    {
        $users = User::get();
        foreach ($users as $user) {
            $destinataire = new Destinataire();
            $destinataire->user_Email = $user->Email;
            $destinataire->notification_id = $notificationId;
            $destinataire->Lu = false;
            $destinataire->save();
        }
    }

    public function notifyNewCours($people, $notificationId)
    {
        foreach ($people as $user) {
            $destinataire = new Destinataire();
            $destinataire->user_Email = $user;
            $destinataire->notification_id = $notificationId;
            $destinataire->Lu = false;
            $destinataire->save();
        }
    }

    public function markAsRead(DestinataireRequest $request)
    {
        $notificationIds = $request->Notifications;
        $notificationsArray = explode(',', $notificationIds);
        $email = $request->User;
        foreach ($notificationsArray as $notificationId) {
            $destinataire = Destinataire::where('notification_id', $notificationId)->where('user_Email', $email)->first();
            $destinataire->Lu = true;
            $destinataire->save();
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
    public function update(Request $request)
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

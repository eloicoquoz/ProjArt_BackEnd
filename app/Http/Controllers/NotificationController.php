<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Notification;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class NotificationController extends Controller
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
     * Créer une notification pour un utilisateut
     *
     * @return la notification créée
     */
    public function createNotification(Request $request){
        $destinataires = $request->destinataire_Email;
        $destinataires = explode(',', $destinataires);
        $notification = app('App\Http\Controllers\NotificationController')->store($request->Titre, $request->Description,$request->user_Email);
        $reussite = true;
        foreach ($destinataires as $destinataire) {
            $existDestinataire = User::where('Email', $destinataire)->first();
            $existClasse = Classe::where('id', $destinataire)->first();
            if($existDestinataire != null){
                $destinataire = app('App\Http\Controllers\DestinataireController')->notifyNewPerson($existDestinataire, $notification->id);
            } else if($existClasse != null){
                $usersInCourse = array();
                $usersInClass = $existClasse->users()->get();
                    foreach ($usersInClass as $user) {
                        $theUser = $user->Email;
                        if ($theUser) {
                            $usersInCourse[] = $user->Email;
                        }
                    }
                $usersInCourse = array_unique($usersInCourse);
                $destinataires = app('App\Http\Controllers\DestinataireController')->notifyNewCours($usersInCourse, $notification->id);
            }  else{
                $reussite = false;
            }
        }
        if($reussite){
            echo "Notification envoyée ";
        }else{
            echo "Destinataire non trouvé : ".$destinataire." Merci de les séparer par des virgules";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($titre, $description, $user)
    {
        $date = date('Y-m-d H:i:s');
        $notification = new Notification();
        $notification->Objet = $titre;
        $notification->Message = $description;
        $notification->EnvoiHeureDate = $date;
        $notification->user_Email = $user;
        $notification->save();
        return $notification;
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

    /**
     * Recherche les notifications d'un utilisateur
     *
     * @return liste des notifications
     */
    public function getNotificationsForUser($email)
    {
        $notificationRoles = array();
        $notificationsReceived = User::where('Email', $email)->first()->destinataires()->get('notification_id');
        $notifications = array();
        foreach ($notificationsReceived as $notificationReceived) {
            $notifications[] = Notification::where('id', $notificationReceived->notification_id)->first();
        }
        // Tri des notifications par date d'envoi (en ordre décroissant)
        $notification = collect($notifications)->sortByDesc('EnvoiHeureDate');
        $notifications = array_reverse($notifications);
        foreach ($notifications as $notification) {
            $rolesSender = User::where('Email', $notification->user_Email)->first()->roles()->get();
            $rolesArray = array();
            foreach ($rolesSender as $role) {
                array_push($rolesArray, $role->id);
            }
            $status = $notification->destinataires()->where('user_Email', $email)->first()->Lu;
            $notificationAndRoles = ['notification' => $notification, 'status' => $status, 'roles' => $rolesArray];
            array_push($notificationRoles, $notificationAndRoles);
        }
        return $notificationRoles;
    }
}

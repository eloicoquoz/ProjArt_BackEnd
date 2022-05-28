<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapingController extends Controller
{
    public function getPersonalTimetable($user='', $pwd=''){

        //date is now, if date is before august, then year is previous year
        $date = date('Y-m-d');
        $year = date('Y');
        if(strtotime($date) < strtotime('August 31')){
            $year = $year - 1;
            $trimestre = 1;
        } else {
            $trimestre = 3;
        }

        $params = array(
            'login' => $user,
            'password' => $pwd,
            'annee' => $year,
            'trimestre' => $trimestre,
            'type' => 2
        );
        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login='.$user.'&password='.$pwd.'&submit=Entrer&annee='.$year.'&trimestre='.$trimestre.'&type=2';
        $response = Http::post($url);
        $lessons = $response->find('tr.lessonRow');
        print_r($lessons);
    }
}

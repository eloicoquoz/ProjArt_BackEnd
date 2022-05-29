<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapingController extends Controller
{
    public function getPersonalTimetable($user='', $pwd=''){
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
        $response = Http::get($url);
        $dom = new DOMDocument();
        @$dom->loadHTML($response->body());
        $xpath = new DOMXPath($dom);
        $weeklyLessonsLists = $xpath->query("//table[@class='lessonsList']");
        $lessons=array();
        foreach ($weeklyLessonsLists as $weeklyLessonsList) {
            $weeklyLessons = $xpath->query(".//tr[@class='lessonRow']", $weeklyLessonsList);
            foreach ($weeklyLessons as $weeklyLesson) {
                $lesson = $xpath->query(".//td", $weeklyLesson);
                $lessonDate = $lesson->item(0)->nodeValue;
                $lessonHours = $lesson->item(1)->nodeValue;
                $lessonName = $lesson->item(2)->nodeValue;
                $lessonTeacher = $lesson->item(3)->nodeValue;
                $lessonRoom = $lesson->item(4)->nodeValue;
                $lesson = [
                    'date' => $lessonDate,
                    'hours' => $lessonHours,
                    'name' => $lessonName,
                    'teacher' => $lessonTeacher,
                    'room' => $lessonRoom
                ];
                $lessons[] = $lesson;
            }
        }

        echo '<table border="1">';
        echo '<tr><th>Date</th><th>Heures</th><th>Nom</th><th>Enseignant</th><th>Salle</th></tr>';
        foreach ($lessons as $lesson) {
            echo '<tr>';
            echo '<td>'.$lesson['date'].'</td>';
            echo '<td>'.$lesson['hours'].'</td>';
            echo '<td>'.$lesson['name'].'</td>';
            echo '<td>'.$lesson['teacher'].'</td>';
            echo '<td>'.$lesson['room'].'</td>';
            echo '</tr>';
        }
        echo '</table>';


    }
}

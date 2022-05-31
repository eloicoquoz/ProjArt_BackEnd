<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use App\Models\User;
use App\Models\Cours;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapingController extends Controller
{
    public function getPersonalTimetable($user='jules.sandoz', $pwd='gUZw428u/S}^Jgff'){
        $date = date('m d');
        $year = date('Y');
        $trimestre = 1;
        if(strtotime('now') < strtotime('31 August')){
            $year = $year - 1;
            if (strtotime('13 February') < strtotime('now')){
                $trimestre = 3;
            }   
        }

        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login='.$user.'&password='.$pwd.'&submit=Entrer&annee='.$year.'&trimestre='.$trimestre.'&type=2';
        $response = Http::get($url);
        $dom = new DOMDocument();
        @$dom->loadHTML($response->body());
        $xpath = new DOMXPath($dom);
        $weeklyLessonsLists = $xpath->query("//table[@class='lessonsList']");
        $lessons = array();
        $matters = array();
        foreach ($weeklyLessonsLists as $weeklyLessonsList) {
            $weeklyLessons = $xpath->query(".//tr[@class='lessonRow']", $weeklyLessonsList);
            foreach ($weeklyLessons as $weeklyLesson) {
                $lesson = $xpath->query(".//td", $weeklyLesson);
                $lessonDate = $lesson->item(0)->nodeValue;
                $lessonHours = $lesson->item(1)->nodeValue;
                $lessonHours = str_replace(' ', '', substr($lessonHours, 0, strpos($lessonHours, '(')));
                $lessonStart = substr($lessonHours, 0, strpos($lessonHours, '-'));
                $lessonEnd = substr($lessonHours, strpos($lessonHours, '-') + 1);
                $lessonName = $lesson->item(2)->nodeValue;
                $class = substr($lessonName, strpos($lessonName, '-')+1, 1);
                $lessonName = substr($lessonName, 0, strpos($lessonName, '-'));
                $lessonTeacher = $lesson->item(3)->nodeValue;
                $lessonRoom = $lesson->item(4)->nodeValue;
                $lesson = [
                    'date' => $lessonDate,
                    'start' => $lessonStart,
                    'end' => $lessonEnd,
                    'label' => $lessonName,
                    'class' => $class,
                    'teacher' => $lessonTeacher,
                    'room' => $lessonRoom
                ];
                $lessons[] = $lesson;
                $matters[] = $lesson['name'];
            }
        }
        $matters = array_unique($matters);
    }

    public function sauvegarderUserMatieres($username, $matters){
        $user = User::where('username', $username)->first();
        $user->matieres()->detach();
        foreach($matters as $matter){
            $matiere = Matiere::where('name', $matter)->first();
            if($matiere){
                $user->matieres()->attach($matiere->id);
            } else {
                $matiere = new Matiere();
                $matiere->name = $matter;
                $matiere->save();
                $user->matieres()->attach($matiere->id);
            }
        }
    }

    public function sauvegarderCours($lessons, $matters){
        //for each lesson, check if there is an existing cours matching the start and room and where and create it attaching it to the matching matiere if it doesn't exist yet
    }

    public function trouverClasse($matters, $year){
        //convert letters into their corresponding place in the alphabet
        $anneesMatieres = array();
        $classes = array();
        $anneeClasse = array();
        foreach($matters as $matter){
            $classes = [$matter => ord(substr($matter, strpos($matter, '-')+1, 1)) - ord('A')+1];
            $nomMatiere = substr($matter, 0, strpos($matter, '-'));
            $anneesMatieres[] = [$matter => checkYear($nomMatiere)];
            $anneeClasse[] = [checkYear($nomMatiere) => ord(substr($matter, strpos($matter, '-')+1, 1)) - ord('A')+1];
        }
        $classesPersonne = array();
        foreach($anneeClasse as $annee => $classe){
            $annee--;
            $nomClasse = 'M'+ $year-$annee-1971 + '-' + $classe;
            $classePersonne[] = $nomClasse;
        }
    }

}

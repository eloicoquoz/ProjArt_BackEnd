<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use App\Models\User;
use App\Models\Cours;
use App\Models\Salle;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapingController extends Controller
{
    public function getPersonalTimetable($user = 'jules.sandoz@heig-vd.ch', $pwd = '')
    {
        $date = date('m d');
        $year = date('Y');
        $trimestre = 1;
        if (strtotime('now') < strtotime('31 August')) {
            $year = $year - 1;
            if (strtotime('13 February') < strtotime('now')) {
                $trimestre = 3;
            }
        }

        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login=' . $user . '&password=' . $pwd . '&submit=Entrer&annee=' . $year . '&trimestre=' . $trimestre . '&type=2';
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
                $class = substr($lessonName, strpos($lessonName, '-') + 1, 1);
                $lessonName = str_replace('E-comm', 'Ecomm', $lessonName);
                $lessonName = str_replace('OPT-IOT', 'OPTIOT', $lessonName);
                $lessonName = str_replace('OPT-TMARK', 'OPTTMARK', $lessonName);
                $lessonName = str_replace('OPT-VR', 'OPTVR', $lessonName);
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
        $this->sauvegarderMatieres($user, $matters);
        $this->sauvegarderCours($user, $lessons, $year);
    }

    public function sauvegarderMatieres($email, $matters)
    {
        $user = User::where('email', $email)->first();
        // $user->matieres()->detach();
        foreach ($matters as $matter) {
            $matiere = Matiere::where('id', $matter)->first();
            if (!$matiere) {
                $matiere = new Matiere();
                $matiere->id = $matter;
                $matiere->Annee = $this->getYearForMatiere($matter);
                $matiere->save();
            }
            $matiereUser = $user->matieres()->where('matiere_id', $matiere->id)->first();
            if (!$matiereUser) {
                $user->matieres()->attach($matiere);
            }
        }
    }

    public function sauvegarderCours($email, $lessons, $year)
    {
        $user = User::where('email', $email)->first();
        // $user->cours()->detach();
        foreach ($lessons as $lesson) {

            $sallesString = str_replace(' ', '', $lesson['room']);
            $sallesArray = explode(',', $sallesString);

            //Vérification de l'existence des salles et ajout si nécessaire
            foreach ($sallesArray as $idSalle) {
                $salle = Salle::where('id', $idSalle)->first();
                if (!$salle) {
                    $salle = new Salle();
                    $salle->id = $idSalle;
                    $salle->save();
                }
            }
            //cours enregistré(s) ayant la même date/heure de début, de fin et enseignant la même matière
            $coursCorrespondants = Cours::where('Debut', $lesson['start'])->where('Fin', $lesson['end'])->where('matiere_id', $lesson['label'])->get();
            $cours=null;
            if($coursCorrespondants){
                $salleAjoutee = false;
                $indexCours = 0;
                //pour chaque cours correspondant, vérification des salles correspondantes pour voir si c'est le même cours ou un cours similaire se passant au même moment
                do{
                    $coursCorrespondant = $coursCorrespondants[$indexCours];
                    $sallesCoursCorrespondant = $coursCorrespondant->salles()->get()->toArray();
                    $sallesCoursCorrespondant = array_column($sallesCoursCorrespondant, 'id');
                    $sallesManquantes = array();
                    $checkSalle = false;
                    foreach ($sallesArray as $idSalle) {
                        if (in_array($idSalle, $sallesCoursCorrespondant)) {
                            $checkSalle = true;
                        } else {
                            $sallesManquantes[] = $idSalle;
                        }
                    }
                    if ($checkSalle) {
                        foreach ($sallesManquantes as $idSalle) {
                            $salle = Salle::where('id', $idSalle)->first();
                            $coursCorrespondant->salles()->attach($salle);
                        }
                        $salleAjoutee = true;
                        $cours = $coursCorrespondant;
                    }
                    $indexCours++;
                    //la boucle s'arrête dès qu'on trouve un cours identique ou dès qu'on a passé par tous les cours correspondants
                } while ($salleAjoutee == false && $indexCours < $coursCorrespondants->count());
                //si aucun cours identique n'a été trouvé, on crée un nouveau cours
                if (!$salleAjoutee) {
                    $cours = new Cours();
                    $cours->Debut = $lesson['start'];
                    $cours->Fin = $lesson['end'];
                    $cours->matiere_id = $lesson['label'];
                    $cours->save();
                    foreach ($sallesArray as $idSalle) {
                        $salle = Salle::where('id', $idSalle)->first();
                        $cours->salles()->attach($salle);
                    }
                }
                $idClasse = $this->trouverClasseCours($lesson, $cours, $year);
                $userClasse = $user->classes()->where('id', $idClasse)->first();
                if (!$userClasse) {
                    $user->classes()->attach($idClasse);
                }
            }
            //on vérifie si le cours est déjà attaché à l'utilisateur
            $userCours = $user->cours()->where('id', $cours->id)->first();
            //si le cours n'est pas attaché à l'utilisateur, on le fait
            if (!$userCours) {
                $user->cours()->attach($cours);
            }
        }
    }

    public function getYearForMatiere($matiere)
    {
        $one = array('MarkDig1', 'Ang1', 'MedSerGam', 'ProtoEnv', 'DocWeb', 'BaseProg1', 'MéthOut', 'BaseMath1', 'FondMedias', 'ComHum', 'MarDévProd', 'DeDonAInf1', 'RechAnPub', 'EvolMétMéd', 'EcrireWeb', 'Ang2', 'GesBudget', 'BaseProg2', 'ComVisuel', 'ProdConMé1', 'BaseMath2', 'ProgServ1', 'MarkDig2', 'InfraDon1', 'DeDonAInf2', 'AnalysMar', 'PilotFin', 'Droit1');
        $two = array('ProgWeb', 'EcoPrint', 'WebDon', 'ProdConMé2', 'InfraDonn2', 'ArchiDép', 'MétRecher', 'Ecomm', 'Socio', 'StratMarq', 'TechAv', 'DévProdMéd', 'WebMobUI', 'PropVal', 'ConceptUI', 'UXDesign', 'VenteProj', 'LabVeilSoc', 'VisualDon', 'Droit2', 'ProjArt');
        $three = array('ArchInfoUX', 'ArchiOWeb', 'BusPlan', 'DévMobil', 'EvalOptPro', 'LabVeilTec', 'ProfilPro', 'Startup', 'SysComplex', 'UXLab', 'ApproMédia', 'CRUNCH', 'OPTIOT', 'OPTTMARK', 'OPTVR', 'ProjInt', 'Stage');
        if (in_array($matiere, $one)) {
            return 1;
        } else if (in_array($matiere, $two)) {
            return 2;
        } else if (in_array($matiere, $three)) {
            return 3;
        }
    }

    public function sauvegarderClasse($email, $classes)
    {
        $user = User::where('email', $email)->first();
        // $user->classes()->detach();
        foreach ($classes as $class) {
            $classe = Classe::where('id', $class)->first();
            if (!$classe) {
                $classe = new Classe();
                $classe->id = $class;
                $classe->save();
            }
            $classeUser = $user->classes()->where('id', $classe->id)->first();
            if (!$classeUser) {
                $user->classes()->attach($classe);
            }
        }
    }

    public function trouverClasse($matters, $year)
    {
        $anneeClasse = array();
        foreach ($matters as $matter) {
            $nomMatiere = substr($matter, 0, strpos($matter, '-'));
            if ($nomMatiere != 'Ang1' && $nomMatiere != 'Ang2') {
                $anneeClasse[] = [$this->checkYear($nomMatiere) => ord(substr($matter, strpos($matter, '-') + 1, 1)) - ord('A') + 1];
            }
        }
        $classesPersonne = array();
        foreach ($anneeClasse as $annee => $classe) {
            $annee--;
            $nomClasse = 'IM' . idate('Y', $year) - $annee - 1971 . '-' . $classe;
            $classesPersonne[] = $nomClasse;
        }
        $classesPersonne = array_unique($classesPersonne);
        return $classesPersonne;
    }

    public function trouverClasseCours($lesson, $cours, $year){
        $anneeCours = ($cours->Annee) - 1;
        $numeroClasse = ord(substr($lesson['label'], strpos($lesson['label'], '-') + 1, 1)) - ord('A') + 1;
        $nomClasse = 'IM' . idate('Y', $year) - $anneeCours - 1971 . '-' . $numeroClasse;
        return $nomClasse;
    }

    // public function enregistrerClasseCours($lessons, $year)
    // {
    //     foreach ($lessons as $lesson) {
    //         $nomMatiere = substr($lesson['label'], 0, strpos($lesson['label'], '-'));
    //         if ($nomMatiere != 'Ang1' && $nomMatiere != 'Ang2') {
    //             $anneeClasse[] = [$this->checkYear($nomMatiere) => ord(substr($lesson['label'], strpos($lesson['label'], '-') + 1, 1)) - ord('A') + 1];
    //         }
    //     }
    // }

    public function checkYear($matter)
    {
        $matiere = Matiere::where('id', $matter)->first();
        $annee = $matiere->Annee;
        return $annee;
    }
}

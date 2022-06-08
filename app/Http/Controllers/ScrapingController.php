<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use App\Models\Cours;
use App\Models\Salle;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapingController extends Controller
{
    /**
     * Gets the timetable of a user from the GAPS website and saves it in the database
     * 
     * @param user your HEIG-VD email
     * @param pwd the password of the user
     * @param fullName The full name of the user.
     */
    public function getPersonalTimetable($user, $pwd, $fullName)
    {
        $year = date('Y');
        $trimestre = 1;
        /* Adaptation de la date au système GAPS (l'année doit correspondre à l'année de début de l'année scolaire) */
        if (strtotime('now') < strtotime('31 August')) {
            $year = $year - 1;
            /* Passage au 2ème semestre */
            if (strtotime('13 February') < strtotime('now')) {
                $trimestre = 3;
            }
        }
        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login=' . urlencode($user) . '&password=' . urlencode($pwd) . '&submit=Entrer&annee=' . $year . '&trimestre=' . $trimestre . '&type=2';
        $response = $this->timetableFromURL($url);
        $lessons = $response[0];
        $matters = $response[1];
        $utilisateur = User::where('Email', $user)->first();
        $this->lierMatieresUser($utilisateur->Email, $matters);
        $this->sauvegarderCoursUser($utilisateur->Email, $lessons, $year);
    }

    public function getTimetablesForClass($user, $pwd, $class, $schoolDepartment)
    {
        $year = date('Y');
        $trimestre = 1;
        /* Adaptation de la date au système GAPS (l'année doit correspondre à l'année de début de l'année scolaire) */
        if (strtotime('now') < strtotime('31 August')) {
            $year = $year - 1;
            /* Passage au 2ème semestre */
            if (strtotime('13 February') < strtotime('now')) {
                $trimestre = 3;
            }
        }
        $url = 'https://gaps.heig-vd.ch/consultation/horaires/?login=' . urlencode($user) . '&password=' . urlencode($pwd) . '&submit=Entrer&annee=' . $year . '&trimestre=' . $trimestre . '&type=9&id=' . $class;
        $response = $this->timetableFromURL($url);
        $lessons = $response[0];
        $matters = $response[1];
        $departement = Departement::where('id', $schoolDepartment)->first();
        if (!$departement) {
            $departement = new Departement();
            $departement->id = $schoolDepartment;
            $departement->save();
        }
        $classe = Classe::where('id', $class)->first();
        if (!$classe) {
            $classe = new Classe();
            $classe->id = $class;
            $classe->departement_id = $schoolDepartment;
            $classe->save();
        }
        $this->creerMatieres($matters);
        $this->sauvegarderCoursClasse($classe->id, $lessons);
    }

    public function timetableFromURL($url)
    {
        $response = Http::get($url);
        $dom = new DOMDocument();
        @$dom->loadHTML($response->body());
        $xpath = new DOMXPath($dom);
        /* Searching for the tables containing the weekly lesson lists. */
        $weeklyLessonsLists = $xpath->query("//table[@class='lessonsList']");
        $lessons = array();
        $matters = array();
        /* Parsing the HTML code and extracting the data from it. */
        foreach ($weeklyLessonsLists as $weeklyLessonsList) {
            /* Searching for individual lesson rows in the table */
            $weeklyLessons = $xpath->query(".//tr[@class='lessonRow']", $weeklyLessonsList);
            /* Parsing the HTML code and extracting data from each row*/
            foreach ($weeklyLessons as $weeklyLesson) {
                $lesson = $xpath->query(".//td", $weeklyLesson);
                $lessonDate = $lesson->item(0)->nodeValue;
                /* Adjusting date and times to right format */
                $lessonDate = explode(".", str_replace(' ', '', substr($lessonDate, strpos($lessonDate, ' '))));
                $lessonDate = $lessonDate[2] . "-" . $lessonDate[1] . "-" . $lessonDate[0];
                $lessonHours = $lesson->item(1)->nodeValue;
                $lessonHours = str_replace(' ', '', substr($lessonHours, 0, strpos($lessonHours, '(')));
                $lessonStart = substr($lessonHours, 0, strpos($lessonHours, '-')) . ':00';
                $lessonEnd = substr($lessonHours, strpos($lessonHours, '-') + 1) . ':00';
                $lessonName = $lesson->item(2)->nodeValue;
                /* Grabbing the letter representing the class */
                $class = substr($lessonName, strpos($lessonName, '-') + 1, 1);
                /* Replacing the '-' in class names for bug prevention */
                $lessonName = str_replace('E-comm', 'Ecomm', $lessonName);
                $lessonName = str_replace('OPT-IOT', 'OPTIOT', $lessonName);
                $lessonName = str_replace('OPT-TMARK', 'OPTTMARK', $lessonName);
                $lessonName = str_replace('OPT-VR', 'OPTVR', $lessonName);
                /* Cutting the end of the strings to get only the course's name */
                $lessonName = substr($lessonName, 0, strpos($lessonName, '-'));
                $lessonTeacher = str_replace(' ', '', $lesson->item(3)->nodeValue);
                $lessonRoom = preg_replace('/\xc2\xa0/', '', $lesson->item(4)->nodeValue);
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
                $matters[] = $lesson['label'];
            }
        }
        /* Removing duplicate values from the array. */
        $matters = array_unique($matters);
        return [$lessons, $matters];
    }

    /**
     * It saves the user's subjects in the database
     * 
     * @param email the email of the user
     * @param matters an array of matiere ids
     */
    public function lierMatieresUser($email, $matters)
    {
        $user = app('App\Http\Controllers\UserController')->show($email);
        $this->creerMatieres($matters);
        foreach ($matters as $matter) {
            $matiere = Matiere::where('id', $matter)->first();
            $matiereUser = $user->matieres()->where('matiere_id', $matiere->id)->first();
            if (!$matiereUser) {
                $user->matieres()->attach($matiere);
            }
        }
    }

    public function creerMatieres($matters)
    {
        foreach ($matters as $matter) {
            $matiere = Matiere::where('id', $matter)->first();
            if (!$matiere) {
                $matiere = new Matiere();
                $matiere->id = $matter;
                $matiere->Annee = $this->getYearForMatiere($matter);
                $matiere->save();
            }
        }
    }

    /**
     * It takes an array of lessons, checks if the rooms exist in the database, checks if the lessons
     * exist in the database, if they do, it adds the rooms to the lesson, if they don't, it creates a
     * new lesson, then it checks if the user is already registered to the lesson, if not, it registers
     * the user to the lesson
     * 
     * @param email the user's email
     * @param lessons an array of lessons, each lesson is an array with the following keys: date, start, end, label, class, teacher, room
     * @param year the year of the user's schedule
     */
    public function sauvegarderCoursUser($email, $lessons, $year)
    {
        foreach ($lessons as $lesson) {
            $cours = $this->creerCours($lesson);
            $user = app('App\Http\Controllers\UserController')->show($email);
            if ($cours) {
                $idClasse = $this->trouverClasseCours($lesson, $cours, $year);
                $this->sauvegarderClasse($idClasse, $cours->matiere_id);
                $userClasse = $user->classes()->where('id', $idClasse)->first();
                if (!$userClasse) {
                    $user->classes()->attach($idClasse);
                }
                $classe = Classe::where('id', $idClasse)->first();
                $coursClasse = $classe->cours()->where('id', $cours->id)->first();
                if (!$coursClasse) {
                    $classe->cours()->attach($cours);
                }
                if ($lesson['teacher'] != '') {
                    if (str_contains($lesson['teacher'], ',')) {
                        $teachers = explode(',', $lesson['teacher']);
                        foreach ($teachers as $teacher) {
                            $prof = User::where('Acronyme', $teacher)->first();
                            $profCours = $prof->cours()->where('id', $cours->id)->first();
                            if (!$profCours) {
                                $prof->cours()->attach($cours);
                            }
                        }
                    } else {
                        $prof = User::where('Acronyme', $lesson['teacher'])->first();
                        $profCours = $prof->cours()->where('id', $cours->id)->first();
                        if (!$profCours) {
                            $prof->cours()->attach($cours);
                        }
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
    }

    public function sauvegarderCoursClasse($class, $lessons)
    {
        foreach ($lessons as $lesson) {
            $cours = $this->creerCours($lesson);
            $classe = Classe::where('id', $class)->first();
            $coursClasse = $cours->classes()->where('id', $classe->id)->first();
            if (!$coursClasse) {
                $cours->classes()->attach($classe);
            }
            if ($lesson['teacher'] != '') {
                if (str_contains($lesson['teacher'], ',')) {
                    $teachers = explode(',', $lesson['teacher']);
                    foreach ($teachers as $teacher) {
                        $prof = User::where('Acronyme', $teacher)->first();
                        $profCours = $prof->cours()->where('id', $cours->id)->first();
                        if (!$profCours) {
                            $prof->cours()->attach($cours);
                        }
                    }
                } else {
                    $prof = User::where('Acronyme', $lesson['teacher'])->first();
                    $profCours = $prof->cours()->where('id', $cours->id)->first();
                    if (!$profCours) {
                        $prof->cours()->attach($cours);
                    }
                }
            }
        }
    }


    /**
     * It creates a new lesson in the database if it doesn't exist yet, or updates it if it does
     * 
     * @param lesson an array containing the following keys:
     * 
     * @return Cours object is being returned.
     */
    public function creerCours($lesson)
    {
        $sallesString = str_replace(' ', '', $lesson['room']);
        $sallesString = str_replace('*', '', $sallesString);
        $sallesString = str_replace(' ', '', $sallesString);
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
        $coursCorrespondants = Cours::where('Debut', $lesson['date'] . ' ' . $lesson['start'])->where('Fin', $lesson['date'] . ' ' . $lesson['end'])->where('matiere_id', $lesson['label'])->get();
        $cours = null;
        if ($coursCorrespondants) {
            $salleAjoutee = false;
            $indexCours = 0;
            //pour chaque cours correspondant, vérification des salles correspondantes pour voir si c'est le même cours ou un cours similaire se passant au même moment
            do {
                if (isset($coursCorrespondants[$indexCours])) {
                    $coursCorrespondant = $coursCorrespondants[$indexCours];
                    $sallesCoursCorrespondant = $coursCorrespondant->salles()->get()->toArray();
                    $sallesCoursCorrespondant = array_column($sallesCoursCorrespondant, 'id');
                    $sallesManquantes = array();
                    $checkSalle = false;
                    foreach ($sallesArray as $idSalle) {
                        $idSalle = str_replace(' ', '', $idSalle);
                        if (in_array($idSalle, $sallesCoursCorrespondant)) {
                            $checkSalle = true;
                        } else {
                            $sallesManquantes[] = $idSalle;
                        }
                    }
                    if ($checkSalle) {
                        foreach ($sallesManquantes as $idSalle) {
                            $idSalle = str_replace(' ', '', $idSalle);
                            $salle = Salle::where('id', $idSalle)->first();
                            $coursCorrespondant->salles()->attach($salle);
                        }
                        $salleAjoutee = true;
                        $cours = $coursCorrespondant;
                    }
                }
                $indexCours++;
                //la boucle s'arrête dès qu'on trouve un cours identique ou dès qu'on a passé par tous les cours correspondants
            } while ($salleAjoutee == false && $indexCours < $coursCorrespondants->count());
            //si aucun cours identique n'a été trouvé, on crée un nouveau cours
            if (!$salleAjoutee) {
                $cours = new Cours();
                $cours->Debut = $lesson['date'] . ' ' . $lesson['start'];
                $cours->Fin = $lesson['date'] . ' ' . $lesson['end'];
                $cours->matiere_id = $lesson['label'];
                $cours->save();
                foreach ($sallesArray as $idSalle) {
                    $salle = Salle::where('id', $idSalle)->first();
                    $cours->salles()->attach($salle);
                }
            }
        } else {
            // Si aucun cours correspondant n'est trouvé, on crée un nouveau cours
            $cours = new Cours();
            $cours->Debut = $lesson['date'] . ' ' . $lesson['start'];
            $cours->Fin = $lesson['date'] . ' ' . $lesson['end'];
            $cours->matiere_id = $lesson['label'];
            $cours->save();
            foreach ($sallesArray as $idSalle) {
                $salle = Salle::where('id', $idSalle)->first();
                $cours->salles()->attach($salle);
            }
        }
        return $cours;
    }

    /**
     * It returns the year of the student based on the name of the subject
     * 
     * @param matiere The name of the subject
     * 
     * @return year of the student
     */
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

    /**
     * It returns the department of a given course
     * 
     * @param matiere The name of the course
     * 
     * @return department of the given subject.
     */
    public function getDepartementForMatiere($matiere)
    {
        $comem = array('MarkDig1', 'Ang1', 'MedSerGam', 'ProtoEnv', 'DocWeb', 'BaseProg1', 'MéthOut', 'BaseMath1', 'FondMedias', 'ComHum', 'MarDévProd', 'DeDonAInf1', 'RechAnPub', 'EvolMétMéd', 'EcrireWeb', 'Ang2', 'GesBudget', 'BaseProg2', 'ComVisuel', 'ProdConMé1', 'BaseMath2', 'ProgServ1', 'MarkDig2', 'InfraDon1', 'DeDonAInf2', 'AnalysMar', 'PilotFin', 'Droit1', 'ProgWeb', 'EcoPrint', 'WebDon', 'ProdConMé2', 'InfraDonn2', 'ArchiDép', 'MétRecher', 'Ecomm', 'Socio', 'StratMarq', 'TechAv', 'DévProdMéd', 'WebMobUI', 'PropVal', 'ConceptUI', 'UXDesign', 'VenteProj', 'LabVeilSoc', 'VisualDon', 'Droit2', 'ProjArt', 'ArchInfoUX', 'ArchiOWeb', 'BusPlan', 'DévMobil', 'EvalOptPro', 'LabVeilTec', 'ProfilPro', 'Startup', 'SysComplex', 'UXLab', 'ApproMédia', 'CRUNCH', 'OPTIOT', 'OPTTMARK', 'OPTVR', 'ProjInt', 'Stage');
        $tic = array();
        $tin = array();
        $heg = array();
        if (in_array($matiere, $comem)) {
            return 'COMEM+';
        } else if (in_array($matiere, $tic)) {
            return 'TIC';
        } else if (in_array($matiere, $tin)) {
            return 'TIN';
        } else if (in_array($matiere, $heg)) {
            return 'HEG';
        }
    }

    /**
     * It saves a class to the database
     * 
     * @param class the class id
     * @param matiere the subject
     */
    public function sauvegarderClasse($class, $matiere)
    {
        $classe = Classe::where('id', $class)->first();
        $departement = $this->getDepartementForMatiere($matiere);
        $departementEntry = Departement::where('id', $departement)->first();
        /* If the department doesn't exist, create a new instance of the Departement class and saving it to the database. */
        if (!$departementEntry) {
            $departementEntry = new Departement();
            $departementEntry->id = $departement;
            $departementEntry->save();
        }
        /* If the class doesn't exist, create a new class object and saving it to the database. */
        if (!$classe) {
            $classe = new Classe();
            $classe->id = $class;
            $classe->departement_id = $departement;
            $classe->save();
        }
    }

    /**
     * It takes a list of classes and returns a list of classes
     * 
     * @param matters the list of matters the person is in charge of
     * @param year current year for GAPS
     * 
     * @return classes the person is part of.
     */
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
            $nomClasse = 'M' . intval($year) - $annee - 1971 . '-' . $classe;
            $classesPersonne[] = $nomClasse;
        }
        $classesPersonne = array_unique($classesPersonne);
        return $classesPersonne;
    }

    /**
     * It takes a lesson, a course, and a year, and returns the name of the class that the lesson is
     * for
     * 
     * @param lesson the lesson object
     * @param cours the course object
     * @param year the year of the timetable
     * 
     * @return name of the class.
     */
    public function trouverClasseCours($lesson, $cours, $year)
    {
        $anneeCours = intval($this->getYearForMatiere($cours->matiere_id)) - 1;
        $numeroClasse = ord($lesson['class']) - ord('A') + 1;
        $nomClasse = 'M' . intval($year) - $anneeCours - 1971 . '-' . $numeroClasse;
        return $nomClasse;
    }

    /**
     * It takes a parameter, , and returns the year of the matter
     * 
     * @param matter the id of the subject
     * 
     * @return year the matter is taught in
     */
    public function checkYear($matter)
    {
        $matiere = Matiere::where('id', $matter)->first();
        $annee = $matiere->Annee;
        return $annee;
    }
}

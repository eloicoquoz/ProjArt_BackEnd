<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RemarqueController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users', [UserController::class, 'index']);

// Get all cours
Route::get('/cours', [CoursController::class, 'allCours']);

// Get all events
Route::get('/events', [EventController::class, 'allEvent']);

// Get event by id 
Route::get('/events/id/{id}', [EventController::class, 'EventById']);

// Get event by user
Route::get('/events/user/{user}', [EventController::class, 'EventByUser']);

// Get event by role
Route::get('/events/role/{role}', [EventController::class, 'EventByRole']);

// Get all remarque
Route::get('/remarque', [RemarqueController::class, 'allRemarque']);

// Get remarque by id 
Route::get('/remarque/id/{id}', [RemarqueController::class, 'RemarqueById']);

// Get remarque by user
Route::get('/remarque/user/{user}', [RemarqueController::class, 'RemarqueByUser']);

// Get remarque by classe by matiere
Route::get('/remarque/classe/{classe}/{matiere}', [RemarqueController::class, 'RemarqueByClasseByMatiere']);

// Get all cours all information
Route::get('/cours/info', [CoursController::class, 'allCoursInfo']);

// Get cours by id Information
Route::get('/cours/info/{id}', [CoursController::class, 'CoursByIdInfo']);

// Get cours by classe Information
Route::get('/cours/classe/{classe}', [CoursController::class, 'CoursByClasse']);

// Get cours by classe by matiere
Route::get('/cours/classe/{classe}/{matiere}', [CoursController::class, 'CoursByClasseByMatiere']);

// Get cours by user
Route::get('/cours/user/{user}', [CoursController::class, 'CoursByUser']);

// Get cours by user by matiere
Route::get('/cours/user/{user}/{matiere}', [CoursController::class, 'CoursByUserByMatiere']);

// Get Salle de cours
Route::get('/cours/salle', [CoursController::class, 'SalleByCours']);

// Get cours by matiere
Route::get('/cours/matiere/{matiere}', [CoursController::class, 'CoursByMatiere']);

// Get cours by id
Route::get('/cours/id/{id}', [CoursController::class, 'CoursById']);

// Get all classes
Route::get('/classes', [ClasseController::class, 'allClasse']);

// Get all classes by filiere
Route::get('/classes/filiere/{filiere}', [ClasseController::class, 'ClasseByFiliere']);

// Get Prof by cours
Route::get('/prof/cours/{cours}', [UserController::class, 'ProfByCours']);


Route::get('/php', function () {
    return phpinfo();
});

Route::get('/pdo', function () {
    $myPDO = new PDO('pgsql:host=localhost;dbname=projart', 'postgres', 'root');
	$result = $myPDO->query("SELECT * FROM users ORDER BY id ASC ");
    return $result->fetchAll();
});




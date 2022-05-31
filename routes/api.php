<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\ClasseController;

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

// Get all cours all information
Route::get('/cours/info', [CoursController::class, 'allCoursInfo']);

// Get cours by id Information
Route::get('/cours/info/{id}', [CoursController::class, 'CoursByIdInfo']);

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

Route::get('/login/{password}/{email}', [UserController::class, 'login']);

Route::get('/signup/{password}/{email}/{prenom}/{nom}', [UserController::class, 'signup']);


Route::get('/php', function () {
    return phpinfo();
});

Route::get('/pdo', function () {
    $myPDO = new PDO('pgsql:host=localhost;dbname=projart', 'postgres', 'root');
	$result = $myPDO->query("SELECT * FROM users ORDER BY id ASC ");
    return $result->fetchAll();
});





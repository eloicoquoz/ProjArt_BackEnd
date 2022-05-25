<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HoraireController;

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

//Route::get('/horaires', [HoraireController::class, 'index']);

Route::get('/users', [HoraireController::class, 'index']);

Route::get('/php', function () {
    return phpinfo();
});

Route::get('/pdo', function () {
    $myPDO = new PDO('pgsql:host=localhost;dbname=projart', 'postgres', 'root');
	$result = $myPDO->query("SELECT * FROM users ORDER BY id ASC ");
    return $result->fetchAll();
});




<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\ServiceUserController;
use App\Http\Controllers\InfoUtilisateurController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('commentaires', CommentaireController::class);
Route::apiResource('competences', CompetenceController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('offres', OffreController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('infouser', InfoUtilisateurController::class);

Route::post('/user/create', [AuthController::class, 'register']);
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('serviceuser', [ServiceUserController::class, 'store']);
Route::get('employe', [AuthController::class, 'employe']);

Route::get('/services/{serviceId}/offres', [OffreController::class, 'getOffresByService']);
Route::get('/offres/{offreId}/offres', [OffreController::class, 'getOffresByid']);

Route::get('candidats', [CandidatController::class, 'index']);

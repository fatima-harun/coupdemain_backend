<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\ServiceUserController;
use App\Http\Controllers\InfoUtilisateurController;
use App\Models\Experience;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('commentaires', CommentaireController::class);
Route::apiResource('competences', CompetenceController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('offres', OffreController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('infouser', InfoUtilisateurController::class);

Route::get('/competences', [CompetenceController::class, 'usercompetence']); //le user verra ses propres competences
Route::get('/candidats/{candidatId}/competences', [CompetenceController::class, 'index']);//l'employeur verra les compètences des candidats
Route::put('/competences/{competenceId}', [CompetenceController::class, 'update']);
Route::get('/competences/{id}', [CompetenceController::class, 'show']);
Route::delete('/competences/{id}', [CompetenceController::class, 'destroy']);


Route::put('/experiences/{experienceId}', [ExperienceController::class, 'update']);
Route::get('/candidats/{candidatId}/experiences', [ExperienceController::class, 'index']);
Route::get('/experiences/{id}', [ExperienceController::class, 'show']);
Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);
Route::get('/experiences', [ExperienceController::class, 'userexperience']); //le user verra ses propres competences

Route::post('/candidatures', [CandidatureController::class, 'store']);
Route::get('/candidatures/{offreId}/offre', [CandidatureController::class, 'getCandidaturesByOffre']);
Route::put('/candidatures/{id}/statut', [CandidatureController::class, 'updateStatut']);
Route::get('/notifications', [CandidatureController::class, 'getNotifications']);
Route::patch('notifications/{id}/lu', [CandidatureController::class, 'lu']);
Route::get('/candidatures/recruter', [CandidatureController::class, 'getRecuter']);


// route des visiteurs
Route::post('/user/create', [AuthController::class, 'register']);
// pour celui qui est connecté
Route::middleware('auth')->post('/user/update', [AuthController::class, 'update']);

Route::delete('/user/{userId}/delete', [AuthController::class, 'destroy']);
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('serviceuser', [ServiceUserController::class, 'store']);
Route::get('employe', [AuthController::class, 'employe']);
Route::get('/services/{serviceId}/user', [AuthController::class, 'getCandidatsByService']);
Route::get('/users', [AuthController::class, 'getAllUser']);
Route::put('/users/{id}/status', [AuthController::class, 'status']);
Route::get('/users/employer', [AuthController::class, 'getEmployer']);
Route::get('/users/employeur', [AuthController::class, 'getEmployeur']);



Route::get('/services/{serviceId}/offres', [OffreController::class, 'getOffresByService']);
Route::get('/offres/mesoffres', [OffreController::class, 'ShowMesOffres']);
Route::get('/employeur/offres', [OffreController::class, 'ShowMesOffres'])->middleware('auth');
Route::post('/offres/{offreId}', [OffreController::class, 'update']);
Route::delete('/offres/{offreId}', [OffreController::class, 'destroy']);

Route::middleware('auth:api')->get('/profil', [AuthController::class, 'profil']);

Route::get('candidats', [CandidatController::class, 'index']);
Route::get('/candidats/{candidatId}', [CandidatController::class, 'show']);


Route::get('users/{userId}/recommandations', [CommentaireController::class, 'show']);
Route::post('/candidats/{userId}/comment', [CommentaireController::class, 'addComment']);
Route::put('/commentaires/{commentId}', [CommentaireController::class, 'update']);
Route::delete('/commentaires/{commentId}', [CommentaireController::class, 'destroy']);

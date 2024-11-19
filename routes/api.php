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
use App\Http\Controllers\NotificationController;
use App\Models\Experience;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::apiResource('commentaires', CommentaireController::class);
Route::apiResource('competences', CompetenceController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('infouser', InfoUtilisateurController::class);

Route::middleware(['auth.jwt'])->group(function () {
Route::get('/competences', [CompetenceController::class, 'usercompetence']); //le user verra ses propres competences
Route::get('/candidats/{candidatId}/competences', [CompetenceController::class, 'index']);//l'employeur verra les compètences des candidats
Route::put('/competences/{competenceId}', [CompetenceController::class, 'update']);
Route::get('/competences/{id}', [CompetenceController::class, 'show']);
Route::delete('/competences/{id}', [CompetenceController::class, 'destroy']);
});

Route::middleware(['auth.jwt'])->group(function () {
Route::put('/experiences/{experienceId}', [ExperienceController::class, 'update']);
Route::get('/candidats/{candidatId}/experiences', [ExperienceController::class, 'index']);
Route::get('/experiences/{id}', [ExperienceController::class, 'show']);
Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);
Route::get('/experiences', [ExperienceController::class, 'userexperience']); //le user verra ses propres competences
});
Route::middleware('auth')->post('/candidatures', [CandidatureController::class, 'store']);
Route::middleware('auth')->put('/users/{id}/status',  [AuthController::class, 'status']);

Route::middleware(['auth.jwt'])->group(function () {
Route::get('/candidatures/{offreId}/offre', [CandidatureController::class, 'getCandidaturesByOffre']);
Route::put('/candidatures/{id}/statut', [CandidatureController::class, 'updateStatut']);
Route::get('/notifications', [CandidatureController::class, 'getNotifications']);
Route::patch('notifications/{id}/lu', [CandidatureController::class, 'lu']);
});

// route des visiteurs
Route::post('/user/create', [AuthController::class, 'register']);
Route::post('/user/login', [AuthController::class, 'login']);

// pour celui qui doit se  connecter
Route::middleware(['auth.jwt'])->group(function () {
    Route::delete('/user/{userId}/delete', [AuthController::class, 'destroy']);
    Route::put('/users/{id}/status', [AuthController::class, 'status']);
    Route::post('/user/update', [AuthController::class, 'update']);
    Route::get('/profil', [AuthController::class, 'profil']);
});


//pour l'admin desactiver ou activer un compte

Route::middleware(['role:admin'])->group(function () {
    Route::get('/users/employer', [AuthController::class, 'getEmployer']);
    Route::get('/users/employeur', [AuthController::class, 'getEmployeur']);
    Route::get('/candidatures/recruter', [CandidatureController::class, 'getRecruter']);
});

Route::middleware(['auth.jwt'])->group(function () {
Route::post('serviceuser', [ServiceUserController::class, 'store']);
Route::get('employe', [AuthController::class, 'employe']);
Route::get('/services/{serviceId}/user', [AuthController::class, 'getCandidatsByService']);
});

Route::middleware(['role:admin'])->group(function () {
Route::get('/users', [AuthController::class, 'getAllUser']);
});

Route::get('/services/{serviceId}/offres', [OffreController::class, 'getOffresByService']);

// Route::get('/offres/mesoffres', [OffreController::class, 'ShowMesOffres']);
Route::get('/offres', [OffreController::class, 'index']);
Route::middleware(['role:employeur'])->group(function () {
   Route::get('/employeur/offres', [OffreController::class, 'ShowMesOffres']);
   Route::delete('/offres/{offreId}', [OffreController::class, 'destroy']);
   Route::post('/offres', [OffreController::class, 'store']);
});
Route::get('/offres/{offreId}', [OffreController::class, 'show']);

Route::get('candidats', [CandidatController::class, 'index']);
Route::get('/candidats/{candidatId}', [CandidatController::class, 'show']);

// Route::get('users/{userId}/recommandations', [CommentaireController::class, 'show']);

Route::middleware(['auth.jwt'])->group(function () {
// Route::post('/candidats/{userId}/comment', [CommentaireController::class, 'addComment']);
// Route::put('/commentaires/{commentId}', [CommentaireController::class, 'update']);
// Route::delete('/commentaires/{commentId}', [CommentaireController::class, 'destroy']);
Route::post('/offres/{offreId}', [OffreController::class, 'update']);
});

Route::middleware('auth.jwt')->group(function () {
    Route::post('/commentaires/{candidat_id}', [CommentaireController::class, 'store']);
    Route::get('/commentaires/{candidat_id}', [CommentaireController::class, 'index']);
    Route::put('/commentaires/{id}', [CommentaireController::class, 'update']);
    Route::delete('/commentaires/{id}', [CommentaireController::class, 'destroy']);
});


Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getUserNotifications']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});

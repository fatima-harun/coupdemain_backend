<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\CommentaireController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('commentaires', CommentaireController::class);
Route::apiResource('competences', CompetenceController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('offres', OffreController::class);


Route::post('/user/create', [AuthController::class, 'register']);
Route::post('/user/login', [AuthController::class, 'login']);

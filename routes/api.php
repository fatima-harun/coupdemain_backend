<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentaireController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('commentaires', CommentaireController::class);

Route::post('/user/create/employeur', [AuthController::class, 'registeremployeur']);
Route::post('/user/create/employe', [AuthController::class, 'registeremploye']);
Route::post('/user/create/admin', [AuthController::class, 'registeradmin']);

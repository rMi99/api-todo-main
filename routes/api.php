<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class,'createUser']);
Route::post('/login', [AuthController::class,'loginUser']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class,'getUser']);
});
Route::post('create',[TaskController::class,'store']);
Route::get('show/{searchId}/{userId}',[TaskController::class,'show']);
Route::get('task/{id}',[TaskController::class,'task']);
Route::put('update/{id}',[TaskController::class,'update']);
Route::delete('delete/{id}',[TaskController::class,'destroy']);
Route::post('/logout',[AuthController::class, 'logout']);
// Route::get('/',[TaskController::class, 'index']);

// Route::get('auth/google', [ GoogleAuthController::class,'redirectToGoogle'])->name('auth-google');
// Route::get('auth/google/callback', [ GoogleAuthController::class,'handleGoogleCallback']);

Route::get('auth', [GoogleAuthController::class, 'redirectToAuth']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleAuthCallback']);

Route::get('/user/{id}', [UserController::class,'user']);
Route::put('/user/change/{id}', [UserController::class,'userChange']);

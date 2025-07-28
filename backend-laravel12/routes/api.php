<?php

use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\DestinationApiController;
use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/refresh', [AuthApiController::class, 'refresh']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::get('logout', [AuthApiController::class, 'logout']);
    Route::get('check-token', [AuthApiController::class, 'checkTokenValidity']);
});

Route::get('/destinations', [DestinationApiController::class, 'index']);
Route::get('/destinations/{id}', [DestinationApiController::class, 'show']);
Route::post('/destinations', [DestinationApiController::class, 'store']);
Route::put('/destinations/{id}', [DestinationApiController::class, 'update']);
Route::delete('/destinations/{id}', [DestinationApiController::class, 'destroy']);

Route::get('/messages', [MessageApiController::class, 'index']);
Route::get('/messages/{id}', [MessageApiController::class, 'show']);
Route::post('/messages', [MessageApiController::class, 'store']);
Route::put('/messages/{id}', [MessageApiController::class, 'update']);
Route::delete('/messages/{id}', [MessageApiController::class, 'destroy']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/users/{id}', [UserApiController::class, 'show']);
    Route::post('/users', [UserApiController::class, 'store']);
    Route::put('/users/{id}', [UserApiController::class, 'update']);
    Route::delete('/users/{id}', [UserApiController::class, 'destroy']);
});

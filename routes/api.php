<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServersController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']); 
    

    // Server
    Route::post('/create-server', [ServersController::class, 'store']);
    Route::get('/get-servers', [ServersController::class, 'index']);
    Route::put('/devices/{id}', [ServersController::class, 'update']);
    Route::get('/server/{id}', [ServersController::class, 'show']);
    Route::delete('/devices/{id}', [ServersController::class, 'destroy']);
});

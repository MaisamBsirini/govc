<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\ComplaintController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you register API routes for your mobile app. These routes
| are stateless and use Sanctum tokens for authentication.
|
*/

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED ROUTES (ONLY FOR LOGGED USERS)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::patch('/complaints/{id}/status', [ComplaintController::class, 'updateStatus']);
    Route::post('/complaints/{id}/note', [ComplaintController::class, 'addNote']);
});



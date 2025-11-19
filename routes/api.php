<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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


});

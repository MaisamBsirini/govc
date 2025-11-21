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

Route::get('/test-role', function () {
    return 'Works!';
})->middleware('role:admin');


// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/employeeLogin', [AuthController::class, 'employeeLogin']);


Route::middleware(['auth:sanctum','role:admin'])->group(function () {

    Route::post('createAccount', [AuthController::class, 'createAccount']);

});

Route::middleware(['auth:sanctum','role:employee'])->group(function () {


});

Route::middleware(['auth:sanctum','role:citizen'])->group(function () {


});


// PROTECTED ROUTES (ONLY FOR LOGGED USERS)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::patch('/complaints/{id}/status', [ComplaintController::class, 'updateStatus']);
    Route::post('/complaints/{id}/note', [ComplaintController::class, 'addNote']);
});



<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;


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


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('getOneComplaint/{id}', [ComplaintController::class, 'getOneComplaint']);
});

Route::middleware(['auth:sanctum','role:admin'])->group(function () {
    Route::post('createAccount', [AuthController::class, 'createAccount']);
    Route::get('getAllComplaints', [ComplaintController::class, 'getAllComplaints']);
    Route::get('getUsers', [ComplaintController::class, 'getUsers']);
});

Route::middleware(['auth:sanctum','role:employee'])->group(function () {
    Route::get('getComplaintsEmployee', [ComplaintController::class, 'getComplaintsEmployee']);
    Route::post('updateStatusAddNote/{id}', [ComplaintController::class, 'updateStatus']);
});

Route::middleware(['auth:sanctum','role:citizen'])->group(function () {
    Route::post('addComplaint', [ComplaintController::class, 'addComplaint']);
    Route::get('getComplaintsCitizen', [ComplaintController::class, 'getComplaintsCitizen']);
});



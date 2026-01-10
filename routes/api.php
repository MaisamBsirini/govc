<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you register API routes for your mobile app. These routes
| are stateless and use Sanctum tokens for authentication.
|
*/

Route::middleware(['auth:sanctum', 'role:admin'])->get('/test', function () {
    return 'OK';
});



// ---------------- AUTH ROUTES ----------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/employeeLogin', [AuthController::class, 'employeeLogin']);

// ---------------- Routes عامة للمستخدمين المسجلين ----------------
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('getOneComplaint/{id}', [ComplaintController::class, 'getOneComplaint']);
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getUserNotifications']);
});

// ---------------- Routes للـ Admin ----------------
Route::middleware(['auth:sanctum','role:admin','log.requests'])->group(function () {
    Route::get('/exportCSV', [ReportController::class, 'exportCSV']);
    Route::get('/exportPDF', [ReportController::class, 'exportPDF']);
    Route::post('createAccount', [AuthController::class, 'createAccount']);
    Route::get('getAllComplaints', [ComplaintController::class, 'getAllComplaints']);
    Route::get('getUsers', [ComplaintController::class, 'getUsers']);
    Route::delete('deleteAccount/{userID}', [AuthController::class, 'deleteAccount']);

});

// ---------------- Routes للـ Employee ----------------
Route::middleware(['auth:sanctum','role:employee','log.requests'])->group(function () {
    Route::get('getComplaintsEmployee', [ComplaintController::class, 'getComplaintsEmployee']);
    Route::post('updateStatusAddNote/{id}', [ComplaintController::class, 'updateStatus']);
});

// ---------------- Routes للـ Citizen ----------------
Route::middleware(['auth:sanctum','role:citizen','log.requests'])->group(function () {
    Route::post('addComplaint', [ComplaintController::class, 'addComplaint']);
    Route::get('getComplaintsCitizen', [ComplaintController::class, 'getComplaintsCitizen']);
});

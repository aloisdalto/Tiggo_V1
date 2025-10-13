<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas públicas
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('services', [ServiceController::class, 'index']);

// Rutas autenticadas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::get('service-requests', [ServiceRequestController::class, 'index']);
    Route::post('service-requests', [ServiceRequestController::class, 'store']);
    Route::post('ratings', [RatingController::class, 'store']);
    Route::get('ratings/technician/{technicianId}', [RatingController::class, 'indexByTechnician']);
});

// Rutas para técnicos
Route::middleware(['auth:sanctum', 'role:tecnico'])->group(function () {
    Route::get('technicians', [TechnicianController::class, 'index']);
    Route::get('technician/service-requests', [ServiceRequestController::class, 'index']);
    Route::patch('service-requests/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus']);
});

// Rutas para administradores
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('users', [AdminController::class, 'listUsers']);
    Route::post('services', [AdminController::class, 'createService']);
    Route::put('services/{service}', [AdminController::class, 'updateService']);
    Route::delete('services/{service}', [AdminController::class, 'deleteService']);
    Route::get('reports', [AdminController::class, 'reports']);
});
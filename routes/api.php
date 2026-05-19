<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\WashOrderController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WashLaneController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Services
    Route::apiResource('services', ServiceController::class);
    
    // Wash Orders
    Route::apiResource('wash-orders', WashOrderController::class);
    Route::patch('/wash-orders/{id}/status', [WashOrderController::class, 'updateStatus']);
    Route::patch('/wash-orders/{id}/payment', [WashOrderController::class, 'updatePayment']);
    
    // Vehicles
    Route::get('/vehicles/search', [VehicleController::class, 'search']);
    Route::get('/vehicles/{id}/history', [VehicleController::class, 'getVehicleHistory']);
    
    // Wash Lanes
    Route::get('/wash-lanes', [WashLaneController::class, 'index']);
    Route::get('/wash-lanes/{id}', [WashLaneController::class, 'show']);
    Route::get('/wash-lanes/available', [WashLaneController::class, 'getAvailable']);
    
    // Staff
    Route::get('/staff', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Staff::where('is_active', true)->get()
        ]);
    });
    
    // Commission Settings
    Route::get('/commission', [CommissionController::class, 'index']);
});
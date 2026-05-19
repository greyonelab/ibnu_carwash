<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\WashOrderController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\CommissionController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\WashLaneController;
use App\Http\Controllers\QueueDisplayController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    
    // Wash Orders - Full CRUD
    Route::get('/orders', [WashOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [WashOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [WashOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [WashOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [WashOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{id}', [WashOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [WashOrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{id}/receipt', [WashOrderController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/orders/{id}/status', [WashOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{id}/payment', [WashOrderController::class, 'updatePayment'])->name('orders.update-payment');
    Route::patch('/orders/{id}/complete', [WashOrderController::class, 'complete'])->name('orders.complete');
    
    // Services Management - Full CRUD
    Route::resource('services', ServiceController::class);
    
    // Staff Management - Full CRUD
    Route::resource('staff', StaffController::class);
    
    // Wash Lanes Management - Full CRUD
    Route::resource('wash-lanes', WashLaneController::class);
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-orders', [ReportsController::class, 'exportOrders'])->name('reports.export-orders');
    Route::get('/reports/export-reports', [ReportsController::class, 'exportReports'])->name('reports.export-reports');
    
    // Commission Settings
    Route::get('/commission', [CommissionController::class, 'index'])->name('commission.index');
    Route::put('/commission', [CommissionController::class, 'update'])->name('commission.update');
});

// Public Queue Display (no auth required)
Route::get('/queue-display', [QueueDisplayController::class, 'index'])->name('queue-display.index');
Route::get('/api/queue-display', [QueueDisplayController::class, 'api'])->name('queue-display.api');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\WashOrderController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\CommissionController;

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
    
    // Wash Orders
    Route::get('/orders', [WashOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [WashOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [WashOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [WashOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [WashOrderController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/orders/{id}/status', [WashOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{id}/payment', [WashOrderController::class, 'updatePayment'])->name('orders.update-payment');
    
    // Services Management
    Route::get('/services', function () {
        $services = \App\Models\Service::all();
        return view('services.index', compact('services'));
    })->name('services.index');
    
    // Staff Management
    Route::get('/staff', function () {
        $staff = \App\Models\Staff::all();
        return view('staff.index', compact('staff'));
    })->name('staff.index');
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-orders', [ReportsController::class, 'exportOrders'])->name('reports.export-orders');
    Route::get('/reports/export-reports', [ReportsController::class, 'exportReports'])->name('reports.export-reports');
    
    // Commission Settings
    Route::get('/commission', [CommissionController::class, 'index'])->name('commission.index');
    Route::put('/commission', [CommissionController::class, 'update'])->name('commission.update');
});

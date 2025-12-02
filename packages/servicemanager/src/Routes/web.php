<?php

use Illuminate\Support\Facades\Route;
use YourCompany\ServiceManager\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Service Manager Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard routes
Route::prefix('service-manager')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('servicemanager.dashboard');
    Route::get('/services', [DashboardController::class, 'services'])->name('servicemanager.services');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('servicemanager.billing');
    Route::get('/plans', [DashboardController::class, 'plans'])->name('servicemanager.plans');
    
    // API endpoints for dashboard
    Route::get('/api/stats', [DashboardController::class, 'apiStats'])->name('servicemanager.api.stats');
    Route::get('/api/revenue', [DashboardController::class, 'apiMonthlyRevenue'])->name('servicemanager.api.revenue');
}); 
<?php

use Illuminate\Support\Facades\Route;
use YourCompany\ServiceManager\Controllers\ServiceController;
use YourCompany\ServiceManager\Controllers\ServicePlanController;
use YourCompany\ServiceManager\Controllers\BillingController;

/*
|--------------------------------------------------------------------------
| Service Manager API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/service-manager')->group(function () {
    
    // Test route
    Route::get('/test', function() {
        return response()->json([
            'success' => true,
            'message' => 'Service Manager package is working!',
            'timestamp' => now()->toISOString()
        ]);
    });
    
    // Service Plans
    Route::prefix('plans')->group(function () {
        Route::get('/', [ServicePlanController::class, 'index']);
        Route::get('/categories', [ServicePlanController::class, 'categories']);
        Route::get('/{id}', [ServicePlanController::class, 'show']);
        Route::get('/{id}/billing-periods', [ServicePlanController::class, 'billingPeriods']);
        Route::post('/{id}/calculate-price', [ServicePlanController::class, 'calculatePrice']);
        
        // Admin routes (no auth for testing)
        Route::post('/', [ServicePlanController::class, 'store']);
        Route::put('/{id}', [ServicePlanController::class, 'update']);
        Route::delete('/{id}', [ServicePlanController::class, 'destroy']);
    });

    // Services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/summary', [ServiceController::class, 'summary']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/{id}', [ServiceController::class, 'show']);
        Route::put('/{id}/resources', [ServiceController::class, 'updateResources']);
        Route::post('/{id}/calculate-resource-change', [ServiceController::class, 'calculateResourceChangeCost']);
        Route::post('/{id}/suspend', [ServiceController::class, 'suspend']);
        Route::post('/{id}/reactivate', [ServiceController::class, 'reactivate']);
        Route::post('/{id}/terminate', [ServiceController::class, 'terminate']);
        Route::get('/{id}/billing-history', [ServiceController::class, 'billingHistory']);
        Route::get('/{id}/resource-usage-history', [ServiceController::class, 'resourceUsageHistory']);
    });

    // Billing
    Route::prefix('billing')->group(function () {
        Route::get('/balance', [BillingController::class, 'balance']);
        Route::post('/add-funds', [BillingController::class, 'addFunds']);
        Route::get('/transactions', [BillingController::class, 'transactions']);
        Route::get('/records', [BillingController::class, 'billingRecords']);
        Route::get('/summary', [BillingController::class, 'summary']);
        Route::get('/overdue', [BillingController::class, 'overdue']);
        Route::post('/pay/{billingId}', [BillingController::class, 'payBilling']);
        
        // Admin routes (no auth for testing)
        Route::post('/process-cycle', [BillingController::class, 'processBillingCycle']);
        Route::get('/statistics', [BillingController::class, 'statistics']);
    });
}); 
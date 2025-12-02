<?php

use Illuminate\Support\Facades\Route;
use YourCompany\MongoCrud\Controllers\Demo01Controller;

/*
|--------------------------------------------------------------------------
| MongoDB CRUD API Routes
|--------------------------------------------------------------------------
*/

// Check if routes are enabled
if (config('mongocrud.enable_routes', true)) {
    
    $prefix = config('mongocrud.route_prefix', 'api/mongo-crud');
    $middleware = config('mongocrud.security.middleware', []);
    
    Route::prefix($prefix)->middleware($middleware)->group(function () {
        
        // Test route
        Route::get('/test', function() {
            return response()->json([
                'success' => true,
                'message' => 'MongoDB CRUD package is working!',
                'timestamp' => now()->toISOString(),
                'package' => 'yourcompany/mongo-crud'
            ]);
        });
        
        // Demo01 CRUD routes
        Route::prefix('demo01')->group(function () {
            // Standard CRUD
            Route::get('/', [Demo01Controller::class, 'index']);           // GET /api/mongo-crud/demo01
            Route::post('/', [Demo01Controller::class, 'store']);          // POST /api/mongo-crud/demo01
            Route::get('/{id}', [Demo01Controller::class, 'show']);        // GET /api/mongo-crud/demo01/{id}
            Route::put('/{id}', [Demo01Controller::class, 'update']);      // PUT /api/mongo-crud/demo01/{id}
            Route::delete('/{id}', [Demo01Controller::class, 'destroy']);  // DELETE /api/mongo-crud/demo01/{id}
            
            // Additional routes
            Route::get('/stats/overview', [Demo01Controller::class, 'stats']);  // GET /api/mongo-crud/demo01/stats/overview
            Route::post('/bulk', [Demo01Controller::class, 'bulk']);            // POST /api/mongo-crud/demo01/bulk
        });
        
    });
} 
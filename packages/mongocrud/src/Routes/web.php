<?php

use Illuminate\Support\Facades\Route;
use YourCompany\MongoCrud\Controllers\WebController;

/*
|--------------------------------------------------------------------------
| MongoDB CRUD Web Routes
|--------------------------------------------------------------------------
*/

// Check if routes are enabled
if (config('mongocrud.enable_routes', true)) {
    
    $prefix = config('mongocrud.web_route_prefix', 'mongo-crud');
    $middleware = config('mongocrud.security.web_middleware', []);
    
    Route::prefix($prefix)->middleware($middleware)->group(function () {
        
        // Dashboard
        Route::get('/', [WebController::class, 'dashboard'])->name('mongocrud.dashboard');
        Route::get('/dashboard', [WebController::class, 'dashboard'])->name('mongocrud.dashboard.index');
        
        // Demo01 Management
        Route::prefix('demo01')->name('mongocrud.demo01.')->group(function () {
            Route::get('/', [WebController::class, 'demo01Index'])->name('index');
            Route::get('/create', [WebController::class, 'demo01Create'])->name('create');
            Route::get('/{id}', [WebController::class, 'demo01Show'])->name('show');
            Route::get('/{id}/edit', [WebController::class, 'demo01Edit'])->name('edit');
        });
        
    });
} 
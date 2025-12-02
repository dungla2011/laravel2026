<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

// ============================================
// Helper function to register auth routes
// ============================================
$registerAuthRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';
    
    // Login
    Route2::get('/login', [
        \App\Http\Controllers\LoginController::class, 'login',
    ])->name('login.login' . $suffix);
    
    Route2::post('/post-login', [
        \App\Http\Controllers\LoginController::class, 'postLogin',
    ])->name('post.login' . $suffix);
    
    Route2::get('/post-login', [
        \App\Http\Controllers\LoginController::class, 'postLogin',
    ])->name('get.login' . $suffix);
    
    // Register
    Route2::get('/register', [
        \App\Http\Controllers\LoginController::class, 'register',
    ])->name('auth.register' . $suffix);
    
    Route2::post('/register', [
        \App\Http\Controllers\LoginController::class, 'register',
    ])->name('auth.registerPost' . $suffix);
    
    // Reset Password
    Route2::get('/reset-password', [
        \App\Http\Controllers\LoginController::class, 'resetPassword',
    ])->name('auth.resetPassword' . $suffix);
    
    Route2::post('/reset-password', [
        \App\Http\Controllers\LoginController::class, 'resetPassword',
    ])->name('auth.resetPasswordPost' . $suffix);
    
    Route2::get('/reset-password-act', [
        \App\Http\Controllers\LoginController::class, 'resetPasswordAct',
    ])->name('auth.resetPasswordAct' . $suffix);
    
    Route2::post('/reset-password-act', [
        \App\Http\Controllers\LoginController::class, 'resetPasswordAct',
    ])->name('auth.resetPasswordAct.Post' . $suffix);
    
    // Active Account
    Route2::get('/active-account', [
        \App\Http\Controllers\LoginController::class, 'activeAccount',
    ])->name('auth.activeAccount' . $suffix);
    
    Route2::post('/active-account', [
        \App\Http\Controllers\LoginController::class, 'activeAccount',
    ])->name('auth.activeAccount.Post' . $suffix);
    
    // Logout
    Route2::get('/logout', [
        \App\Http\Controllers\LoginController::class, 'logout',
    ])->name('logout' . $suffix);
};

// ============================================
// Routes WITHOUT locale prefix (default vi)
// ============================================
Route::middleware(['setlocale'])->group(function () use ($registerAuthRoutes) {
    $registerAuthRoutes(false);
});

// ============================================
// Routes WITH locale prefix (/vi, /en, /ja, /ko...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
// ============================================
Route::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['setlocale'])
    ->group(function () use ($registerAuthRoutes) {
        $registerAuthRoutes(true);
    });

// ============================================
// Google OAuth (No locale prefix)
// ============================================
Route::get('auth/google', [
    \App\Http\Controllers\LoginController::class, 'redirectToGoogle'
])->name('auth.google');

Route::get('google/callback', [
    \App\Http\Controllers\LoginController::class, 'handleGoogleCallback'
])->name('google.callback');

Route::post('/api/auth/google-mobile', [
    \App\Http\Controllers\LoginController::class, 'handleGoogleMobile'
])->name('auth.google-mobile');

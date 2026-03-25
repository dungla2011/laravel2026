<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;


///////////////////////////////////////////////////////////////////////////////
// ============================================
// Affiliate Program Route with Multi-language Support
// ============================================
$registerAffiliateProgramRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::get('/service/cloud-vps', [
        \App\Http\Controllers\IndexController::class, 'cloudVps',
    ])->name('service.cloud-vps' . $suffix);
};

// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($registerAffiliateProgramRoutes) {
    $registerAffiliateProgramRoutes(false);
});

// Routes WITH locale prefix (/vi, /en, /ja, /ko, ...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerAffiliateProgramRoutes) {
        $registerAffiliateProgramRoutes(true);
    });

///////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////
// ============================================
// Affiliate Program Route with Multi-language Support
// ============================================
$registerAffiliateProgramRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::get('/service/standard-vps', [
        \App\Http\Controllers\IndexController::class, 'basicVps',
    ])->name('service.standard-vps' . $suffix);
};

// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($registerAffiliateProgramRoutes) {
    $registerAffiliateProgramRoutes(false);
});

// Routes WITH locale prefix (/vi, /en, /ja, /ko, ...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerAffiliateProgramRoutes) {
        $registerAffiliateProgramRoutes(true);
    });

///////////////////////////////////////////////////////////////////////////////

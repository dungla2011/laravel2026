<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

// ============================================
// Redirect default language URLs to non-prefixed URLs
// Example: /vi/pricing → /pricing (SEO: avoid duplicate content)
// ============================================
//Có lẽ ko nên rediect,, và vẫn lên tường minh có /<default language>
// $defaultLang = \clang1::getDefaultLanguage();
// Route::get('/' . $defaultLang . '/{path?}', function($path = null) {
//     return redirect('/' . ($path ?: ''), 301);
// })->where('path', '.*')->name('redirect.default.locale');

// ============================================
// Public Homepage Route with Multi-language Support
// ============================================
$registerPublicRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route::match(['get', 'post'], '/', [
        \App\Http\Controllers\IndexController::class, 'public',
    ])->name('public' . $suffix)
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
};

// Routes WITHOUT locale prefix (default vi) - /
Route::middleware(['web', 'setlocale'])->group(function() use ($registerPublicRoutes) {
    $registerPublicRoutes(false);
});

// Routes WITH locale prefix - /vi, /en, /ja, /ko, etc.
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerPublicRoutes) {
        $registerPublicRoutes(true);
    });




///////////////////////////////////////////////////////////////////////////////
// ============================================
// Buy VIP Route with Multi-language Support
// ============================================
$registerBuyVipRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::match(['get', 'post'], '/buy-vip', [
        \App\Http\Controllers\OrderItemController::class, 'buyVip',
    ])->name('buy.vip' . $suffix)
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
};

// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($registerBuyVipRoutes) {
    $registerBuyVipRoutes(false);
});

// Routes WITH locale prefix (/vi, /en, /ja, /ko, ...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerBuyVipRoutes) {
        $registerBuyVipRoutes(true);
    });



////////////////////////////

$registerOurService = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';
    Route2::match(['get', 'post'], '/our-services', [
        \App\Http\Controllers\OrderItemController::class, 'buyVip',
    ])->name('our-services' . $suffix)
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
};
// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($registerOurService) {
    $registerOurService(false);
});

// Routes WITH locale prefix (/vi, /en, /ja, /ko, ...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerOurService) {
        $registerOurService(true);
    });
/////////////////////////////////////////xxx//////////////////////////////////////
// ============================================
// Pricing Route with Multi-language Support
// ============================================
$registerPricingRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::get('/pricing', [
        \App\Http\Controllers\OrderItemController::class, 'buyVip',
    ])->name('pricing_buy.vip' . $suffix)
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
};

// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($registerPricingRoutes) {
    $registerPricingRoutes(false);
});

// Routes WITH locale prefix (/vi, /en, /ja, /ko, ...)
// ✅ Cho phép TẤT CẢ locale (bao gồm cả default 'vi')
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($registerPricingRoutes) {
        $registerPricingRoutes(true);
    });


///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
// ============================================
// Affiliate Program Route with Multi-language Support
// ============================================
$registerAffiliateProgramRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::get('/affiliate-program', [
        \App\Http\Controllers\IndexController::class, 'affiliateProgram',
    ])->name('affiliate.program' . $suffix);
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


//Route2::match(['get', 'post'], '/privacy', [
//    \App\Http\Controllers\IndexController::class, 'privacyPolicyPing',
//])->name('public.privacy');


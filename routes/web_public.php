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


// ============================================
// Deposit Routes with Multi-language Support
// ============================================

// 1. Deposit Form - Display form
$depositFormRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::get('/deposit', [
        \App\Http\Controllers\PaymentController::class, 'depositForm',
    ])->name('deposit.form' . $suffix);
};

// Routes WITHOUT locale prefix (default vi)
Route2::middleware(['web', 'setlocale'])->group(function() use ($depositFormRoutes) {
    $depositFormRoutes(false);
});

// Routes WITH locale prefix
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($depositFormRoutes) {
        $depositFormRoutes(true);
    });

// 2. Deposit Payment - Process payment (both GET and POST)
$depositPaymentRoutes = function ($localized = false) {
    $suffix = $localized ? '.localized' : '';

    Route2::match(['get', 'post'], '/deposit/payment', [
        \App\Http\Controllers\PaymentController::class, 'depositPayment',
    ])->name('deposit.payment' . $suffix)
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
};

// Routes WITHOUT locale prefix
Route2::middleware(['web', 'setlocale'])->group(function() use ($depositPaymentRoutes) {
    $depositPaymentRoutes(false);
});

// Routes WITH locale prefix
Route2::prefix('{locale}')
    ->where(['locale' => implode('|', \clang1::getLanguageListKey())])
    ->middleware(['web', 'setlocale'])
    ->group(function() use ($depositPaymentRoutes) {
        $depositPaymentRoutes(true);
    });

// 3. Deposit Webhook - BaoKim callback (POST only, no CSRF)
Route2::post('/deposit/webhook', [
    \App\Http\Controllers\PaymentController::class, 'depositWebhook',
])->name('deposit.webhook')
  ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

///////////////////////////////////////////////////////////////////////////////


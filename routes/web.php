<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// I18N Demo Page
Route::get('/i18n-demo', function () {
    return view('i18n_demo');
})->name('i18n.demo');

// VPS Billing Routes
Route::prefix('/member/vps-billing')->group(function () {
    // Hiển thị báo cáo HTML
    Route::get('/report', [\App\Http\Controllers\VpsBillingController::class, 'show'])
        ->name('vps.billing.report');
    
    // Chi tiết tính phí 1 hàng vps_usages
    Route::get('/report-detail/{id}', [\App\Http\Controllers\VpsBillingController::class, 'showDetail'])
        ->name('vps.billing.detail');
    
    // Download PDF (GET and POST methods)
    Route::match(['GET', 'POST'], '/report/pdf', [\App\Http\Controllers\VpsBillingController::class, 'downloadPdf'])
        ->name('vps.billing.pdf');
    
    // Download Excel
    Route::get('/report/excel', [\App\Http\Controllers\VpsBillingController::class, 'downloadExcel'])
        ->name('vps.billing.excel');
    
    // Gửi email
    Route::get('/send-email', [\App\Http\Controllers\VpsBillingController::class, 'sendEmailForm'])
        ->name('vps.billing.send-email-form');
    
    Route::post('/send-email', [\App\Http\Controllers\VpsBillingController::class, 'sendEmailPage'])
        ->name('vps.billing.send-email');
});

// Note: Dynamic catch-all route moved to web_zzz_dynamic.php
// to ensure it loads LAST after all other routes


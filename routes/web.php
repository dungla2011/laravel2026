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

// Note: Dynamic catch-all route moved to web_zzz_dynamic.php
// to ensure it loads LAST after all other routes


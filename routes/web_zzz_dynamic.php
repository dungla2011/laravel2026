<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic Catch-All Routes
|--------------------------------------------------------------------------
|
| This file MUST load LAST (hence the zzz prefix).
| It catches all URLs that don't match any other route.
|
| Examples: /gioi-thieu, /du-an, /lien-he, /ve-chung-toi
|
| This works with route:cache because it's a single static route.
| The dynamic logic is in DynamicPageController which queries the database.
|
*/

// Dynamic Pages - Catch-all route
// Handles all dynamic URLs from database (MenuTree + BlockUi)
Route::get('/{slug}', [App\Http\Controllers\DynamicPageController::class, 'show'])
    ->where('slug', '.*')
    ->name('dynamic.page');

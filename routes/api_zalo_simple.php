<?php

/**
 * Simple Zalo Proxy API - Catch-all Route
 *
 * File: routes/api.php
 *
 * Thêm route này vào file routes/api.php của Laravel project
 * Route này sẽ forward TẤT CẢ request /api/zalo/* sang Node.js server
 */

use App\Http\ControllerApi\ZaloSimpleProxyController;
use App\Http\ControllerApi\ZaloProxyControllerDEL;
use Illuminate\Support\Facades\Route;

//Xử lý riêng vào Laravel, ko forward sang node js:
Route::get('/zalo/accounts/{name}/get-match-messages', [ZaloSimpleProxyController::class, 'getMatchMessages'])
    ->name('zalo.accounts.get-match-messages2');

/*
|--------------------------------------------------------------------------
| Simple Zalo Proxy Route - Forward Everything
|--------------------------------------------------------------------------
|
| Chỉ cần 1 route duy nhất để forward tất cả request sang Node.js
|
| Ví dụ:
|   /api/zalo/accounts              -> forward sang Node.js
|   /api/zalo/accounts/abc/qr-login -> forward sang Node.js
|   /api/zalo/qr/abc                -> forward sang Node.js
|   /api/zalo/bất-kỳ-path-nào       -> forward sang Node.js
|
*/

Route::any('zalo/accounts', function () {
    //Mã lỗi 403 - Forbidden
    http_response_code(403); //sao mã này ko hoạt động nhỉ?
    echo "Not valid access here!";
    return;

})->name('zalo.proxy.accounts.all');

Route::any('zalo/accounts/{name}/{path}', [ZaloSimpleProxyController::class, 'proxyAccount'])
    ->where('name', '.*') // Cho phép match tất cả ký tự, bao gồm cả /
    ->name('zalo.proxy.account.all1');

Route::any('zalo/accounts/{name}', [ZaloSimpleProxyController::class, 'proxyAccount'])
    ->where('name', '.*') // Cho phép match tất cả ký tự, bao gồm cả /
    ->name('zalo.proxy.account.all2');


/**
 * Catch-all route - Forward mọi request /api/zalo/* sang Node.js
 *
 * Method: ANY (GET, POST, PUT, DELETE, PATCH, ...)
 * Path: /api/zalo/{any}
 *
 * {any} sẽ match với bất kỳ path nào sau /api/zalo/
 */
Route::any('zalo/{any}', [ZaloSimpleProxyController::class, 'proxyToNodejs'])
    ->where('any', '.*') // Cho phép match tất cả ký tự, bao gồm cả /
    ->name('zalo.proxy.all');

/*
|--------------------------------------------------------------------------
| Usage Examples
|--------------------------------------------------------------------------
|
| Request từ client:
|   GET /api/zalo/accounts
|
| Laravel sẽ forward sang:
|   GET http://localhost:3000/api/accounts
|
| Response từ Node.js sẽ được trả về y nguyên cho client
|
|--------------------------------------------------------------------------
|
| Request từ client:
|   POST /api/zalo/accounts/abc/qr-login
|   Body: {"re_login": true}
|
| Laravel sẽ forward sang:
|   POST http://localhost:3000/api/accounts/abc/qr-login
|   Body: {"re_login": true}
|
| Response từ Node.js sẽ được trả về y nguyên cho client
|
*/

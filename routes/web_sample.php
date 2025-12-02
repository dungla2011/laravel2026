<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Http\Controllers\MediaItemController2;
use App\Http\Controllers\MediaFolderController2;
use App\Http\Controllers\_CommonController;
use App\Http\ControllerApi\_CommonControllerApi;


Route2::prefix('_admin')->group(function () {
    // Route với action và id: _admin/vps-plan/edit/4
    Route2::get('/{model_name}/{action}/{id}', [_CommonController::class, 'handleActionWithId'])
        ->middleware('can:common.action')
        ->name('common.action.id');

    // Route với action: _admin/vps-plan/list, _admin/order-info/create
    Route2::get('/{model_name}/{action}', [_CommonController::class, 'handleAction'])
        ->middleware('can:common.action')
        ->name('common.action');

    // Route không action - mặc định list: _admin/vps-plan
    Route2::get('/{model_name}', [_CommonController::class, 'handleActionDefault'])
        ->middleware('can:common.action')
        ->name('common.list');
});

// API Routes
Route2::prefix('_api')->group(function () {
    // Get single item: _api/vps-plan/get/4
    Route2::get('/{model_name}/get/{id}', [_CommonControllerApi::class, 'handleGet'])
        ->name('api.get');

    // Add/Create: _api/vps-plan/add
    Route2::post('/{model_name}/add', [_CommonControllerApi::class, 'handleAdd'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.add');

    // Update: _api/vps-plan/update/4
    Route2::post('/{model_name}/update/{id}', [_CommonControllerApi::class, 'handleUpdate'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.update');

    // Delete: _api/vps-plan/delete?id=4
    Route2::delete('/{model_name}/delete', [_CommonControllerApi::class, 'handleDelete'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.delete');
});

// Media Items Routes
Route::prefix('media')->name('media.')->group(function () {
    // Items
    Route::resource('items', MediaItemController2::class);
    Route::get('folders/{folder}/items', [MediaItemController2::class, 'getItemsByFolder'])
        ->name('folders.items');

    // Folders (nếu bạn cần thêm Controller cho Folders)
    // Route::resource('folders', MediaFolderController::class);
});



Route2::get('/run-multi-zalo-pc', function () {
    return redirect('/');
});



$routeName = 'privacy-01';
$r = Route2::get('/privacy-policy', [
    \App\Http\Controllers\IndexController::class, 'privacyPolicy',
])->name($routeName);

$routeName = 'download_1k';
$r = Route2::get('/apiDownload1k', [
    \App\Http\ControllerApi\DownloadFileControllerApi::class, 'apiDownload1k',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;



$routeName = 'momoReturn';
$r = Route2::get('/buy-vip/momoReturn', [
    \App\Http\Controllers\OrderItemController::class, 'momoReturn',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;

$routeName = 'momoNotify';
$r = Route2::get('/buy-vip/momoNotify', [
    \App\Http\Controllers\OrderItemController::class, 'momoNotify',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;


$routeName = 'buy.vip.post';
$r = Route2::post('/buy-vip', [
    \App\Http\Controllers\OrderItemController::class, 'buyVip',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;


$routeName = 'webhook_bk_1k.vip.post';
$r = Route2::post('/webhookBk', [
    \App\Http\Controllers\OrderItemController::class, 'webHookBK',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;


$routeName = 'webhook_bk_1k.vip.get';
$r = Route2::get('/webhookBk', [
    \App\Http\Controllers\OrderItemController::class, 'webHookBK',
])->name($routeName)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;


//////////////////////
$routeName = 'task.member.list';
$r = Route2::get('/my-task', [
    \App\Http\Controllers\HrTaskController::class, 'list_task',
])->name($routeName);

Route::view('/test123456', 'admin.demo-api.test3');

Route::view('/admin/hr-cham-cong', 'admin.hr.hr-cham-cong');
Route::view('/admin/hr-cham-cong2', 'admin.hr.hr-cham-cong2');
//Route::view('/admin/hr-salary-month', 'admin.demo-api.hr-salary-user-month');
Route::view('/member/hr-cham-cong', 'admin.hr.hr-cham-cong');

Route::view('/admin/hr-sample-timeframe1', 'admin.hr.demo-timeframe1');

Route::view('/admin/hr-user-expense-month', 'admin.hr.user-expense-month');


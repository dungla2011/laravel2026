<?php

use App\Components\Route2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route2::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route2::prefix('/common')->group(function () {
//    $route_group_desc = "API - Save Meta Data";
//
//    $nameModule = 'common';
//    $cls = \App\Http\ControllerApi\CommonControllerApi::class;
//    $routeName = "api.".$nameModule.".save-meta-data";
//    $r = Route2::match(array('GET', 'POST'), "/save-meta-data", [
//        $cls, 'saveMetaData',
//    ])->name($routeName);
//    $r->middleware("can:".$routeName);
//    $r->route_group_desc_ = $route_group_desc;
//    $r->route_desc_ = "Save Meta Data";
//});

Route2::match(['GET', 'POST'], '/get-language', [
    \App\Http\Controllers\IndexController::class
    , 'getMobileLanguage',
])->name('get.mobile.language');

Route2::match(['GET', 'POST'], '/get-available-languages', [
    \App\Http\Controllers\IndexController::class
    , 'getAvaiableMobileLanguage',
])->name('get.mobile.available.language');


//////////////////////////////////////
Route2::prefix('/')->group(function () {
    $route_group_desc = 'API - LOGIN';
    $nameModule = 'common';
    $routeName = 'api.'.$nameModule.'.login-api';
    $r = Route2::match(['POST'], '/login-api', [
        \App\Http\ControllerApi\CommonControllerApi::class, 'loginApi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'API LOGIN';
});

Route2::prefix('/common')->group(function () {
    $route_group_desc = 'API - Meta copyFromTable';

    $nameModule = 'common';
    $cls = \App\Http\ControllerApi\CommonControllerApi::class;
    $routeName = 'api.'.$nameModule.'.copyFromTable';
    $r = Route2::match(['GET', 'POST'], '/copyFromTable', [
        $cls, 'copyFromTable',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'copyFromTable Meta';
});

Route2::prefix('/common')->group(function () {
    $route_group_desc = 'API - Save Meta Data1';

    $nameModule = 'common';
    $cls = \App\Http\ControllerApi\CommonControllerApi::class;
    $routeName = 'api.'.$nameModule.'.save-meta-data2';
    $r = Route2::match(['GET', 'POST'], '/save-meta-data2', [
        $cls, 'saveMetaData2',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Save Meta Data2';
});

//Route2::prefix('/product')->group(function () {
//    $route_group_desc = "API - Thao tác Sản phẩm";
//    $routeName = "api.product.index";
//    $r = Route2::get("product/index", [
//        \App\Http\Controllers\AdminProductController::class, 'list'
//    ])->name($routeName);
////$r->middleware(['auth:web', "can:" . $routeName]);
//    $r->middleware("can:" . $routeName);
//    $r->route_group_desc_ = $route_group_desc;
//    $r->route_desc_ = "API Lấy list sản phẩm";
//});

Route2::get('/get-app-version',
    [
        \App\Http\Controllers\IndexController::class, 'updateVersion'
    ],
)->name('api.get-app-version');

Route2::prefix('/usertest')->group(function () {
    $route_group_desc = 'API - Thao tác với Demo';

    $cls = \App\Http\ControllerApi\DemoControllerApi::class;
    $nameModule = 'usertest';

    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = "Xem danh sách $nameModule";

    //    $routeName = "api.".$nameModule.".create";
    //    $r = Route2::get("/create",
    //        [$cls, 'create'])
    //        ->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Tạo $nameModule";

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm demo';
    //
    //    $routeName = "api.".$nameModule.".edit";
    //    $r = Route2::get("/edit/{id}", [
    //        $cls, 'edit'
    //    ])->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Sửa $nameModule";
    //
    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = "Cập nhật $nameModule";
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete/{id}', [
        $cls, 'delete',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = "Xóa $nameModule";

});

// Screenshot API - Server-side rendering
Route2::prefix('/screenshot')->group(function () {
    $route_group_desc = "API - Screenshot Service";

    // Health check
    Route2::get('/health', [
        \App\Http\Controllers\ScreenshotController::class, 'health'
    ])->name('api.screenshot.health');

    // General screenshot
    Route2::post('/capture', [
        \App\Http\Controllers\ScreenshotController::class, 'capture'
    ])->name('api.screenshot.capture');

    // SVG-specific screenshot (for genealogy tree)
    Route2::post('/svg', [
        \App\Http\Controllers\ScreenshotController::class, 'captureSvg'
    ])->name('api.screenshot.svg');

    // URL screenshot - Visit real page with cookies and click button
    Route2::post('/url', [
        \App\Http\Controllers\ScreenshotController::class, 'captureUrl'
    ])->name('api.screenshot.url');
});

// Monitor Graph API - Monitoring dashboards with Chart.js
// require __DIR__ . '/api_monitor_graph.php';

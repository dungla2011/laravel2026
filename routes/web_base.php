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

Route2::prefix('admin')->group(function () {

    $routeName = 'admin.index';
    $r = Route2::match(['get'], '/',
        [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.index')->middleware('can:'.$routeName);
    $r->route_desc_ = 'Admin đây';
    //    $r->middleware("can:".$routeName);

    $routeName = 'admin.db-permission';
    $r = Route2::get('/db-permission', [
        \App\Http\Controllers\AdminController::class, 'dbPermission',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = 'DB Permission';
    $r->route_desc_ = 'DB Permission';

    Route2::prefix('categories')->group(function () {

        $routeName = 'admin.categories.create';
        $r = Route2::get('/create',
            [
                //Cũ:
                //        'as'=>'categories.create',
                //        'uses'=>"\App\Http\Controllers\CategoryController@create"
                \App\Http\Controllers\CategoryController::class, 'create',
            ])->name($routeName)->middleware('can:'.$routeName);

        $routeName = 'admin.categories.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CategoryController::class, 'index',
        ])->name($routeName)->middleware('can:'.$routeName);

        $routeName = 'admin.categories.add';
        $r = Route2::post('/add', [
            \App\Http\Controllers\CategoryController::class, 'store',
        ])->name($routeName)->middleware('can:'.$routeName);

        $routeName = 'admin.categories.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CategoryController::class, 'edit',
        ])->name($routeName)->middleware('can:'.$routeName);

        $routeName = 'admin.categories.update';
        $r = Route2::post('/update/{id}', [
            \App\Http\Controllers\CategoryController::class, 'update',
        ])->name($routeName)->middleware('can:'.$routeName);

        $routeName = 'admin.categories.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\CategoryController::class, 'delete',
        ])->name($routeName)->middleware('can:'.$routeName);

    });

    Route2::prefix('demo')->group(function () {
        $route_group_desc = 'Thao tác với Demo';

        $routeName = 'admin.demo.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DemoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.demo.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DemoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'admin.demo.add';
        $r = Route2::post('/add', [
            \App\Http\Controllers\DemoController::class, 'store',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Thêm demo';
        //
        $routeName = 'admin.demo.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DemoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';
        //
        $routeName = 'admin.demo.update';
        $r = Route2::post('/update/{id}', [
            \App\Http\Controllers\DemoController::class, 'update',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Cập nhật demo';
        //
        $routeName = 'admin.demo.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\DemoController::class, 'delete',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xóa demo';

    });
    //
    //    Route2::prefix('site-mng')->group(function () {
    //
    //        $route_group_desc = "Thao tác với SiteMng";
    //
    //        $routeName = "admin.site-mng.index";
    //        $r = Route2::get("/", [
    //            \App\Http\Controllers\SiteMngController::class, 'index',
    //        ])->name($routeName);
    //        $r->middleware("can:".$routeName);
    //        $r->route_group_desc_ = $route_group_desc;
    //        $r->route_desc_ = 'Xem danh sách site-mng';
    //
    //
    //
    //        $routeName = "admin.site-mng.edit";
    //        $r = Route2::get("/edit/{id}", [
    //            \App\Http\Controllers\SiteMngController::class, 'edit'
    //        ])->name($routeName);
    //        $r->middleware("can:".$routeName);
    //        $r->route_group_desc_ = $route_group_desc;
    //        $r->route_desc_ = 'Sửa site-mng';
    //
    //        $routeName = "admin.site-mng.create";
    //        $r = Route2::get("/create",
    //            [\App\Http\Controllers\SiteMngController::class, 'create'])
    //            ->name($routeName);
    //        $r->middleware("can:".$routeName);
    //        $r->route_group_desc_ = $route_group_desc;
    //        $r->route_desc_ = 'Tạo site-mng';
    //
    //    });

});

Route2::prefix('admin')->group(function () {

    Route2::prefix('demogate')->group(function () {
        $route_group_desc = 'Trang DemoGate';
        $r = Route2::get('/index',
            [\App\Http\Controllers\DemoGate::class, 'index'])
            ->name('admin.demogate.index');

        $r->route_desc_ = 'Xem demo gate';
        $r->route_group_desc_ = $route_group_desc;

        $r = Route2::get('/test1',
            [\App\Http\Controllers\DemoGate::class, 'test1']
        )->name('admin.demogate.test1');
        $r->route_desc_ = 'Xem demo gate test1';

        $r = Route2::get('/test2',
            [\App\Http\Controllers\DemoGate::class, 'test2']
        )->name('admin.demogate.test2');
        $r->route_desc_ = 'Xem demo gate test2';

        $r = Route2::get('/test3',
            [\App\Http\Controllers\DemoGate::class, 'test3']
        )->name('admin.demogate.test3')->middleware('can:admin.demogate.test3');
        $r->route_desc_ = 'Xem demo gate test3';

        $r = Route2::get('/common_gate',
            [\App\Http\Controllers\DemoGate::class, 'test3']
        )->name('admin.demogate.common_gate');
        $r->route_desc_ = 'Xem demo gate';

    });
});

Route2::get('/test01', function () {
    return view('test01');
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route2::post('/test01', function () {
    return view('test01');
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route2::get('/tool/auto-insert-route-permission', function () {
    return view('tool.auto-insert-route-permission');
})->name('tool.auto-insert-route-permission');

//Route2::group(['middleware' => 'can'], function() {
//    Route2::resource('task', \App\Http\Controllers\TaskController::class);
//});

Route2::get('/test_download01', [
    \App\Http\Controllers\IndexController::class, 'test_download1',
]);

Route2::get('/test_cloud_file', [
    \App\Http\Controllers\IndexController::class, 'test_cloud_file',
]);

Route2::get('/stop_email', [
    \App\Http\Controllers\IndexController::class, 'stop_email',
]);
Route2::get('/unsubscribe_email', [
    \App\Http\Controllers\IndexController::class, 'stop_email',
]);


Route2::get('/genQrCode/{str}', [
    \App\Http\Controllers\IndexController::class, 'genQrCode',
]);


Route2::get('/game/duoi-hinh-bat-chu', [
    \App\Http\Controllers\IndexController::class, 'duoiHinhBatChu',
]);



Route2::get('/game/tap-danh-may', [
    \App\Http\Controllers\IndexController::class, 'tapDanhMay',
]);


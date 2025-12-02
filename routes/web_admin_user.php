<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {
    Route2::prefix('user-api')->group(function () {
        $route_group_desc = 'Thao tác với User';

        $routeName = 'admin.user-api.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AdminUserUseApiController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách User';

        $routeName = 'admin.user-api.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\AdminUserUseApiController::class, 'edit',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.user-api.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\AdminUserUseApiController::class, 'create'])
            ->name($routeName);
        //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo User';
        //
        //        $routeName = "admin.user-api.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\AdminUserUseApiController::class, 'store'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm User';
        ////
        ////
        //        $routeName = "admin.user-api.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\AdminUserUseApiController::class, 'update'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->middleware("can:" . $routeName);
        //        $r->route_desc_ = 'Cập nhật User';
        ////
        //        $routeName = "admin.user-api.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\AdminUserUseApiController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa User';
    });

    Route2::prefix('user')->group(function () {

        $route_group_desc = 'Thao tác với User';

        $routeName = 'admin.user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AdminUserController::class, 'index',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách user';

        //        $routeName = "admin.user.index";
        //        $r = Route2::get("/api", [
        //            \App\Http\Controllers\AdminUserController::class, 'index',
        //        ])->name($routeName)->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xem danh sách user Grid API';

        $routeName = 'admin.user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\AdminUserController::class, 'create'])
            ->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo user ABC';

        $routeName = 'admin.user.add';
        $r = Route2::post('/add', [
            \App\Http\Controllers\AdminUserController::class, 'store',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Thêm user';

        $routeName = 'admin.user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\AdminUserController::class, 'edit',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa user';

        $routeName = 'admin.user.update';
        $r = Route2::post('/update/{id}', [
            \App\Http\Controllers\AdminUserController::class, 'update',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Cập nhật user';

        $routeName = 'admin.user.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\AdminUserController::class, 'delete',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xóa user';

    });
});

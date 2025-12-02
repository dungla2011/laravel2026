<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('user-cloud')->group(function () {
        $route_group_desc = 'Thao tác với UserCloud';

        $routeName = 'admin.user-cloud.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\UserCloudController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách UserCloud';

        $routeName = 'admin.user-cloud.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\UserCloudController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa UserCloud';

        $routeName = 'admin.user-cloud.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\UserCloudController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo UserCloud';
        //
        //        $routeName = "admin.user-cloud.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\UserCloudController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm UserCloud';
        ////
        //        $routeName = "admin.user-cloud.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\UserCloudController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật UserCloud';
        ////
        //        $routeName = "admin.user-cloud.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\UserCloudController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa UserCloud';
    });
});

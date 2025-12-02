<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('news')->group(function () {
        $route_group_desc = 'Thao tác với Demo';

        $routeName = 'admin.news.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\NewsController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.news.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\NewsController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.news.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\NewsController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';
        //
        //        $routeName = "admin.news.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\NewsApiController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm demo';
        ////
        ////
        //        $routeName = "admin.news.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\NewsApiController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật demo';
        ////
        $routeName = 'admin.news.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\NewsController::class, 'delete',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xóa demo';
    });

});

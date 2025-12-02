<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('demo-and-tag')->group(function () {
        $route_group_desc = 'Thao tác với Demo + Tag';

        $routeName = 'admin.demo-tag.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DemoAndTagController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.demo-tag.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DemoAndTagController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.demo-tag.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DemoAndTagController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';
        //
        //        $routeName = "admin.demo-tag.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\DemoAndTagController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm demo';
        ////
        ////
        //        $routeName = "admin.demo-tag.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\DemoAndTagController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật demo';
        ////
        //        $routeName = "admin.demo-tag.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\DemoAndTagController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa demo';
    });

});

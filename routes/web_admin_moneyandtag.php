<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('money-and-tag')->group(function () {
        $route_group_desc = 'Thao tác với MoneyAndTag';

        $routeName = 'admin.money-and-tag.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MoneyAndTagController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách money-and-tag';

        $routeName = 'admin.money-and-tag.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MoneyAndTagController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa money-and-tag';

        $routeName = 'admin.money-and-tag.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MoneyAndTagController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo money-and-tag';

        //        $routeName = "admin.money-and-tag.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\MoneyAndTagController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm money-and-tag';
        //
        //
        //        $routeName = "admin.money-and-tag.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\MoneyAndTagController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật money-and-tag';
        ////
        //        $routeName = "admin.money-and-tag.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\MoneyAndTagController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa money-and-tag';
    });

});

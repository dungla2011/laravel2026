<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('money-tag')->group(function () {
        $route_group_desc = 'Thao tác với MoneyTag';

        $routeName = 'admin.money-tag.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MoneyTagController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách money-tag';

        $routeName = 'admin.money-tag.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MoneyTagController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa money-tag';

        $routeName = 'admin.money-tag.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MoneyTagController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo money-tag';

        //        $routeName = "admin.money-tag.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\MoneyTagController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm money-tag';
        //
        //
        //        $routeName = "admin.money-tag.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\MoneyTagController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật money-tag';
        ////
        //        $routeName = "admin.money-tag.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\MoneyTagController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa money-tag';
    });

});

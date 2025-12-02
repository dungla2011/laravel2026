<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('money-log')->group(function () {
        $route_group_desc = 'Thao tác với MoneyLog';

        $routeName = 'admin.money-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MoneyLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách money-Log';

        $routeName = 'admin.money-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MoneyLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa money-Log';

        $routeName = 'admin.money-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MoneyLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo money-Log';

        //        $routeName = "admin.money-log.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\MoneyLogController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm money-Log';
        //
        //
        //        $routeName = "admin.money-log.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\MoneyLogController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật money-Log';
        ////
        //        $routeName = "admin.money-log.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\MoneyLogController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa money-Log';
    });

});

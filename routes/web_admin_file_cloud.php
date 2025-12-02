<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('file-cloud')->group(function () {
        $route_group_desc = 'Thao tác với FileCloud';

        $routeName = 'admin.file-cloud.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileCloudController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách FileCloud';

        $routeName = 'admin.file-cloud.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileCloudController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa FileCloud';

        $routeName = 'admin.file-cloud.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileCloudController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo FileCloud';
        //
        //        $routeName = "admin.file-cloud.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\FileCloudController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm FileCloud';
        ////
        ////
        //        $routeName = "admin.file-cloud.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\FileCloudController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật FileCloud';
        ////
        //        $routeName = "admin.file-cloud.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\FileCloudController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa FileCloud';

    });

});

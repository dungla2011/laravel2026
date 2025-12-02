<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('file')->group(function () {
        $route_group_desc = 'Thao tác với File';

        $routeName = 'admin.file.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileUploadController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách File';

        $routeName = 'admin.file.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileUploadController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa File';

        $routeName = 'admin.file.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileUploadController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo File';

        //        $routeName = "admin.file.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\FileUploadController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm File';
        ////
        ////
        //        $routeName = "admin.file.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\FileUploadController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật File';
        ////
        //        $routeName = "admin.file.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\FileUploadController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa File';

        //
        //        $routeName = "admin.file-tree.index";
        //        $r = Route2::get("/tree", [
        //            \App\Http\Controllers\FileUploadController::class, 'tree_index'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Tree  folder';
        //
        //
        //        $routeName = "admin.file.upload";
        //        $r = Route2::get("/upload", [
        //            \App\Http\Controllers\FileUploadController::class, 'upload'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'File upload';

    });

});

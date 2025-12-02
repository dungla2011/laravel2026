<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('member')->group(function () {

    Route2::prefix('folder-file')->group(function () {
        $route_group_desc = 'Thao tác với folder';

        $routeName = 'member.folder-file.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FolderFileController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách  folder';

        $routeName = 'member.folder-file.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FolderFileController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa  folder';

        $routeName = 'member.folder-file.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FolderFileController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo  folder';
        //
        //        $routeName = "member.folder-file.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\FolderFileController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm  folder';
        ////
        ////
        //        $routeName = "member.folder-file.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\FolderFileController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật  folder';
        ////
        //        $routeName = "member.folder-file.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\FolderFileController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa  folder';

        $routeName = 'member.folder-file.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FolderFileController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree  folder';

    });

});

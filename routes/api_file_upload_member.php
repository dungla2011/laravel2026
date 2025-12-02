<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('/member-file')->group(function () {

    $route_group_desc = 'API - Thao tác với file Upload';
    $nameModule = 'member-file';
    $modelUsing_ = \App\Models\FileUpload::class;

    $cls = \App\Http\ControllerApi\FileUploadControllerApi::class;

    $routeName = 'api.member-file.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách file';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-file.create";
    //    $r = Route2::post("/create",
    //        [$cls, 'create'])
    //        ->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Tạo demo";

    $routeName = 'api.member-file.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm file';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-file.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin file';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-file.edit";
    //    $r = Route2::get("/edit/{id}", [
    //        $cls, 'edit'
    //    ])->name($routeName);
    //        if($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Sửa file";
    //    $r->modelUsing_ = $modelUsing_;

    //
    $routeName = 'api.member-file.update';
    $r = \App\Components\Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật file';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-file.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật file - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.member-file.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa file';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-file.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục file';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-file.move";
    //    $r = Route2::get("/move", [
    //        $cls, 'tree_move',
    //    ])->name($routeName);
    //    if ($r instanceof Route2) ;
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Move on tree file";
    //    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-file.upload';
    $r = Route2::match(['GET', 'POST'], '/upload', [
        $cls, 'upload',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Upload file';
    $r->modelUsing_ = $modelUsing_;

});

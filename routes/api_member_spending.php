<?php

use App\Components\Route2;

Route2::prefix('/member-spending')->group(function () {

    $route_group_desc = 'API - Thao tác với Spending';
    $nameModule = 'member-spending';
    $modelUsing_ = \App\Models\Spending::class;

    $cls = \App\Http\ControllerApi\SpendingControllerApi::class;

    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách spending';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin spending';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm spending';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật spending';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật spending - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa spending';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục spending';
    $r->modelUsing_ = $modelUsing_;

    ////FOR TREE - NẾU CÓ //////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.tree-index';
    $r = Route2::get('/tree', [
        $cls, 'tree_index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree list Spending';

    $routeName = 'api.'.$nameModule.'.tree-create';
    $r = Route2::get('/tree-create', [
        $cls, 'tree_create',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo Spending tree';

    $routeName = 'api.'.$nameModule.'.tree-rename';
    $r = Route2::get('/tree-rename', [
        $cls, 'tree_save',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Rename Spending tree';

    $routeName = 'api.'.$nameModule.'.tree-delete';
    $r = Route2::get('/tree-delete', [
        $cls, 'tree_delete',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Delete Spending tree';

    $routeName = 'api.'.$nameModule.'.tree-move';
    $r = Route2::get('/tree-move', [
        $cls, 'tree_move',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Move on Spending tree';

});

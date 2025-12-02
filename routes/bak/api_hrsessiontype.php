<?php

use App\Components\Route2;

Route2::prefix('/hr-session-type')->group(function () {

    $route_group_desc = 'API - Thao tác với HrSessionType';
    $nameModule = 'hr-session-type';
    $modelUsing_ = \App\Models\HrSessionType::class;

    $cls = \App\Http\ControllerApi\HrSessionTypeControllerApi::class;

    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật hr-session-type - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục hr-session-type';
    $r->modelUsing_ = $modelUsing_;

    ////FOR TREE - NẾU CÓ //////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.tree-index';
    $r = Route2::get('/tree', [
        $cls, 'tree_index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree list HrSessionType';

    $routeName = 'api.'.$nameModule.'.tree-create';
    $r = Route2::get('/tree-create', [
        $cls, 'tree_create',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo HrSessionType tree';

    $routeName = 'api.'.$nameModule.'.tree-rename';
    $r = Route2::get('/tree-rename', [
        $cls, 'tree_save',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Rename HrSessionType tree';

    $routeName = 'api.'.$nameModule.'.tree-delete';
    $r = Route2::get('/tree-delete', [
        $cls, 'tree_delete',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Delete HrSessionType tree';

    $routeName = 'api.'.$nameModule.'.tree-move';
    $r = Route2::get('/tree-move', [
        $cls, 'tree_move',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Move on HrSessionType tree';

});

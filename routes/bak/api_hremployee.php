<?php

use App\Components\Route2;

Route2::prefix('/hr-employee')->group(function () {

    $route_group_desc = 'API - Thao tác với HrEmployee';
    $nameModule = 'hr-employee';
    $modelUsing_ = \App\Models\HrEmployee::class;

    $cls = \App\Http\ControllerApi\HrEmployeeControllerApi::class;

    $routeName = 'api.'.$nameModule.'.search_user';
    $r = Route2::post('/search_user', [
        $cls, 'search_user',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tìm user';
    $r->modelUsing_ = $modelUsing_;

    ////////////////////
    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách hr-employee';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin hr-employee';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm hr-employee';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật hr-employee';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật hr-employee - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa hr-employee';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục hr-employee';
    $r->modelUsing_ = $modelUsing_;

    ////FOR TREE - NẾU CÓ //////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.tree-index';
    $r = Route2::get('/tree', [
        $cls, 'tree_index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree list HrEmployee';

    $routeName = 'api.'.$nameModule.'.tree-create';
    $r = Route2::get('/tree-create', [
        $cls, 'tree_create',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo HrEmployee tree';

    $routeName = 'api.'.$nameModule.'.tree-rename';
    $r = Route2::get('/tree-rename', [
        $cls, 'tree_save',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Rename HrEmployee tree';

    $routeName = 'api.'.$nameModule.'.tree-delete';
    $r = Route2::get('/tree-delete', [
        $cls, 'tree_delete',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Delete HrEmployee tree';

    $routeName = 'api.'.$nameModule.'.tree-move';
    $r = Route2::get('/tree-move', [
        $cls, 'tree_move',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Move on HrEmployee tree';

    ////////////////////////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.getHtmlPrintOrder';
    $r = Route2::match(['GET', 'POST'], '/getHtmlPrintProfile', [
        \App\Http\ControllerApi\HrEmployeeControllerApi::class, 'getHtmlPrintProfile',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Get Html để in';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.change_order';
    $r = Route2::match(['GET', 'POST'], '/change_order', [
        \App\Http\ControllerApi\HrEmployeeControllerApi::class, 'change_order',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Change order employee';
    $r->modelUsing_ = $modelUsing_;

});

<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('tree-mng-col-fix')->group(function () {
        $route_group_desc = 'Thao tác với TreeMngColFix';

        $routeName = 'admin.tree-mng-col-fix.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TreeMngColFixController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách tree-mng-col-fix';

        $routeName = 'admin.tree-mng-col-fix.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TreeMngColFixController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa tree-mng-col-fix';

        $routeName = 'admin.tree-mng-col-fix.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TreeMngColFixController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo tree-mng-col-fix';

        $routeName = 'admin.tree-mng-col-fix.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TreeMngColFixController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TreeMngColFix';
    });

});

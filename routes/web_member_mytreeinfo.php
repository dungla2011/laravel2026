<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('my-tree-info')->group(function () {
        $route_group_desc = 'Thao tác với MyTreeInfo Member';

        $routeName = 'member.my-tree-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MyTreeInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách my-tree-info';

        $routeName = 'member.my-tree-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MyTreeInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa my-tree-info';

        $routeName = 'member.my-tree-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MyTreeInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo my-tree-info';

        $routeName = 'member.my-tree-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MyTreeInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MyTreeInfo';
    });

});

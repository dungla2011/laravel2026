<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('tree-mng')->group(function () {
        $route_group_desc = 'Thao tác với GiaPha';

        $routeName = 'member.tree-mng.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TreeMngController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách tree-mng';

        $routeName = 'member.tree-mng.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TreeMngController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa tree-mng';

        $routeName = 'member.tree-mng.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TreeMngController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo tree-mng';

        $routeName = 'member.tree-mng.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TreeMngController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree  giapha';

    });

});

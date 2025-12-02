<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-org-tree')->group(function () {
        $route_group_desc = 'Thao tác với HrOrgTree';

        $routeName = 'admin.hr-org-tree.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrOrgTreeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-org-tree';

        $routeName = 'admin.hr-org-tree.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrOrgTreeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-org-tree';

        $routeName = 'admin.hr-org-tree.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrOrgTreeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-org-tree';

        $routeName = 'admin.hr-org-tree.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrOrgTreeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrOrgTree';
    });

});

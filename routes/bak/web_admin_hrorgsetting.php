<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-org-setting')->group(function () {
        $route_group_desc = 'Thao tác với HrOrgSetting';

        $routeName = 'admin.hr-org-setting.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrOrgSettingController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-org-setting';

        $routeName = 'admin.hr-org-setting.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrOrgSettingController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-org-setting';

        $routeName = 'admin.hr-org-setting.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrOrgSettingController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-org-setting';

        $routeName = 'admin.hr-org-setting.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrOrgSettingController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrOrgSetting';
    });

});

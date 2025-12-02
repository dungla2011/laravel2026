<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-folder')->group(function () {
        $route_group_desc = 'Thao tác với QuizFolder';

        $routeName = 'admin.quiz-folder.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizFolderController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-folder';

        $routeName = 'admin.quiz-folder.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizFolderController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-folder';

        $routeName = 'admin.quiz-folder.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizFolderController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-folder';

        $routeName = 'admin.quiz-folder.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizFolderController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizFolder';
    });

});

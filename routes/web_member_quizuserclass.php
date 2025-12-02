<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('quiz-user-class')->group(function () {
        $route_group_desc = 'Thao tác với QuizUserClass';

        $routeName = 'member.quiz-user-class.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizUserClassController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-user-class';

        $routeName = 'member.quiz-user-class.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizUserClassController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-user-class';

        $routeName = 'member.quiz-user-class.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizUserClassController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-user-class';

        $routeName = 'member.quiz-user-class.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizUserClassController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizUserClass';
    });

});

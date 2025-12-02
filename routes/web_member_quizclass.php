<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('quiz-class')->group(function () {
        $route_group_desc = 'Thao tác với QuizClass';

        $routeName = 'member.quiz-class.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizClassController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-class';

        $routeName = 'member.quiz-class.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizClassController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-class';

        $routeName = 'member.quiz-class.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizClassController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-class';

        $routeName = 'member.quiz-class.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizClassController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizClass';
    });

});



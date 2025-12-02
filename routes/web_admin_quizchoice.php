<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-choice')->group(function () {
        $route_group_desc = 'Thao tác với QuizChoice';

        $routeName = 'admin.quiz-choice.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizChoiceController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-choice';

        $routeName = 'admin.quiz-choice.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizChoiceController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-choice';

        $routeName = 'admin.quiz-choice.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizChoiceController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-choice';

        $routeName = 'admin.quiz-choice.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizChoiceController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizChoice';
    });

});

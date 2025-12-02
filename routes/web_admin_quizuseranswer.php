<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-user-answer')->group(function () {
        $route_group_desc = 'Thao tác với QuizUserAnswer';

        $routeName = 'admin.quiz-user-answer.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizUserAnswerController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-user-answer';

        $routeName = 'admin.quiz-user-answer.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizUserAnswerController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-user-answer';

        $routeName = 'admin.quiz-user-answer.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizUserAnswerController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-user-answer';

        $routeName = 'admin.quiz-user-answer.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizUserAnswerController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizUserAnswer';
    });

});

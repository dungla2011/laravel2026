<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('typing-test-result')->group(function () {
        $route_group_desc = 'Thao tác với TypingTestResult';

        $routeName = 'admin.typing-test-result.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TypingTestResultController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách typing-test-result';

        $routeName = 'admin.typing-test-result.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TypingTestResultController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa typing-test-result';

        $routeName = 'admin.typing-test-result.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TypingTestResultController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo typing-test-result';

        $routeName = 'admin.typing-test-result.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TypingTestResultController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TypingTestResult';
    });

});

Route2::prefix('member')->group(function () {

    Route2::prefix('typing-test-result')->group(function () {
        $route_group_desc = 'Thao tác với TypingTestResult';
        $routeName = 'member.typing-test-result.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TypingTestResultController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách typing-test-result';

    });
});

Route2::prefix('/member/typing-history')->group(function () {

    $route_group_desc = 'Lịch sử Typing H';
    $routeName = 'member.typing-history';
    $r = Route2::get('/', [
        \App\Http\Controllers\TypingTestResultController::class, 'history',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách typing-test-result';

});

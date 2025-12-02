<?php

use App\Components\Route2;

$r = Route2::match(['get'], '/my-tree',
    [\App\Http\Controllers\TreeMngController::class, 'public_tree'])
    ->name('public.my-tree.index');
//
//$r = Route2::match(['post'], "/my-tree",
//    [\App\Http\Controllers\TreeMngController::class, 'public_tree'])
//    ->name("public.news.index");

$r = Route2::match(['get'], '/my-tree/id/{idString}',
    [\App\Http\Controllers\TreeMngController::class, 'public_tree'])
    ->name('public.my-tree.index.id')->where('idString', '[0-9a-z]+');

$r = Route2::match(['get'], '/my-tree-info/{idString}',
    [\App\Http\Controllers\TreeMngController::class, 'tree_info_item'])
    ->name('public.giapha.tieu-su')->where('idString', '[0-9a-z]+');

$r = Route2::match(['get'], '/vip-account',
    [\App\Http\Controllers\TreeMngController::class, 'vip_account'])
    ->name('public.giapha.vip');

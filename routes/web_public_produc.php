<?php

use App\Components\Route2;

$r = Route2::match(['get'], '/san-pham',
    [\App\Http\Controllers\ProductPublicController::class, 'index_public'])
    ->name('public.product.index');

$r = Route2::match(['get'], '/san-pham/danh-muc/{slug}.{id}.html',
    [\App\Http\Controllers\ProductPublicController::class, 'index_public'])
    ->name('public.product.index.id');

$r = Route2::match(['get'], '/san-pham/{slug}.{id}.html',
    [\App\Http\Controllers\ProductPublicController::class, 'item'])
    ->name('public.product.item1');

$r = Route2::match(['get'], '/san-pham/{slug}.{id}',
    [\App\Http\Controllers\ProductPublicController::class, 'item'])
    ->name('public.product.item2');

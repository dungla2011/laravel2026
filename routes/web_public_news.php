<?php

use App\Components\Route2;


$r = Route2::match(['get'], '/tin-tuc',
    [\App\Http\Controllers\NewsPublicController::class, 'index_public'])
    ->name('public.news.index');

$r = Route2::match(['get'], '/tin-tuc/{slug}.{id}.html',
    [\App\Http\Controllers\NewsPublicController::class, 'news_item'])
    ->name('public.news.item1');

$r = Route2::match(['get'], '/tin-tuc/s/{slug}.{id}',
    [\App\Http\Controllers\NewsPublicController::class, 'index_public'])
    ->name('public.news.index.folder');

$r = Route2::match(['get'], '/tin-tuc/{slug}.{id}.s',
    [\App\Http\Controllers\NewsPublicController::class, 'index_public'])
    ->name('public.news.index.item');

$r = Route2::match(['get'], '/tin-tuc/{slug}.{id}',
    [\App\Http\Controllers\NewsPublicController::class, 'news_item'])
    ->name('public.news.item');

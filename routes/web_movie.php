<?php

use App\Components\Route2;

$r = Route2::match(['get'], '/movie/cat/{slug}.{id}',
    [\App\Http\Controllers\MoviePublicController::class, 'cat_view'])
    ->name('movie.cat.index');

$r = Route2::match(['get'], '/movie/item/{slug}.{id}',
    [\App\Http\Controllers\MoviePublicController::class, 'item_view'])
    ->name('movie.item');

$r = Route2::match(['get'], '/movie',
    [\App\Http\Controllers\MoviePublicController::class, 'cat_view'])
    ->name('movie.item2');



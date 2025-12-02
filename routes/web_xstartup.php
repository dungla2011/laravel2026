<?php

use App\Components\Route2;



$routeName = 'privacy-012';
$r = Route2::get('/ke-hoach', [
    \App\Http\Controllers\IndexController::class, 'keHoachKinhDoanh',
])->name($routeName);

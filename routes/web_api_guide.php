<?php

use App\Components\Route2;

Route2::get('/api-export', [
    \App\Http\Controllers\IndexController::class, 'api_export',
]);

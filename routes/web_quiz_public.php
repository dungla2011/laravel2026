<?php

use App\Components\Route2;

$r = Route2::match(['get'], '/quiz/toan-tuyen-chon-7-8-tuoi',
    [\App\Http\Controllers\QuizPublicController::class, 'index'])
    ->name('quiz.index');

$r = Route2::match(['get'], '/quiz/toan-tu-duy-tuyen-chon-toan-dien-tieu-hoc',
    [\App\Http\Controllers\QuizPublicController::class, 'index'])
    ->name('quiz.toan-tu-duy-cap-1');

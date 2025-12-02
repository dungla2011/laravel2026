<?php

use App\Components\Route2;


Route::get('/replicateFile.html', [
    \App\Http\Controllers\DownloadController::class, 'replicateFile4s',
])->name('public.replicateFile4s');

Route::get('/dl-file/{fid}/{name?}', [

    \App\Http\Controllers\DownloadController::class, 'download_one_file',
])->name('public.dl');


Route::get('/search-file', [
    \App\Http\Controllers\Download2Controller::class, 'search_file',
])->name('search_file.dl');
Route::get('/search', [
    \App\Http\Controllers\Download2Controller::class, 'search_file',
])->name('search_file.dl2');

Route::get('/api/download_done_file', [
    \App\Http\Controllers\DownloadController::class, 'download_done_file',
])->name('public.download_done_file');


Route::get('/api/download_check_file', [
    \App\Http\Controllers\DownloadController::class, 'download_check_file',
])->name('public.download_check_file');


Route::get('/dl_file_v2', [
    \App\Http\Controllers\DownloadController::class, 'dl_file_v2',
])->name('public.download_check_file2');





////////////////////////////////////////////
$routeName = '4s.public.file.dl';
$r = Route2::get('/f/{id}', [
    \App\Http\Controllers\Download2Controller::class, 'dlfile',
])->name($routeName);

$routeName = '4s.public.file.dl1';
$r = Route2::get('/f/{id}/', [
    \App\Http\Controllers\Download2Controller::class, 'dlfile',
])->name($routeName);
$routeName = '4s.public.file.dl2';
$r = Route2::get('/f/{id}/{name?}', [
    \App\Http\Controllers\Download2Controller::class, 'dlfile',
])->name($routeName);


$routeName = '4s.public.folder_list.dl';
$r = Route2::get('/d/{id}', [
    \App\Http\Controllers\Download2Controller::class, 'folder_list',
])->name($routeName);

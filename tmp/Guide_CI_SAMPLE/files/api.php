<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/user/list', function () {
    $users = User::select('id', 'name', 'email', 'created_at')->get();
    return response()->json([
        'status' => 'success',
        'data' => $users,
        'count' => $users->count()
    ]);
});

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DEF_TOOL_CMS", 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require_once "/var/www/html/public/index.php";

if (!isSupperAdmin_()) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!preg_match('/^103\.163\.216\./', $ip)) {
        die("Cannot access: IP $ip not allowed");
    }
}

$rows = \App\Models\OrderItem::orderBy('created_at')
    ->get(['user_id', 'created_at', 'price', 'param1']);

$userIds = $rows->pluck('user_id')->unique()->filter()->values();
$users   = \App\Models\User::whereIn('id', $userIds)->pluck('email', 'id');

$result = $rows->map(function ($item) use ($users) {
    return [
        'user_id'    => $item->user_id,
        'email'      => $users[$item->user_id] ?? null,
        'created_at' => $item->created_at,
        'price'      => $item->price,
        'param1'     => $item->param1,
    ];
})->values()->toArray();

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

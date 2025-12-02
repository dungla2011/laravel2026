<?php
require_once "/var/www/html/public/index.php";
\App\Models\User::where('email', 'your_account@gmail.com')->first();//?->forceDelete();
$user = \App\Models\User::where('email', 'test002@gmail.com')->first();//?->forceDelete();
if($user)
if($uid = $user->getId()){
    $user->forceDelete();
    \App\Models\MonitorItem::where('user_id', $uid)->forceDelete();
    \App\Models\MonitorConfig::where('user_id', $uid)->forceDelete();
}

$user = \App\Models\User::where('email', 'test001@gmail.com')->first();//?->forceDelete();
if($user)
if($uid = $user->getId()){
    $user->forceDelete();
    \App\Models\MonitorItem::where('user_id', $uid)->forceDelete();
    \App\Models\MonitorConfig::where('user_id', $uid)->forceDelete();
}
\App\Models\User::addUserAndPassword('test001@gmail.com', 'test001', 'test001@gmail.com111');

<?php

require_once __DIR__.'/public/index.php';

//$cmd = 'php artisan schema:dump --prune';
//echo "\n CMD = $cmd";
//exec($cmd);

$cmd = 'php artisan migrate:generate --squash';
$cmd = 'Gõ lệnh này vào:\nphp artisan migrate:generate';
echo "\n CMD = $cmd";

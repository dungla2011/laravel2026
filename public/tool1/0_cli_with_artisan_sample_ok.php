<?php

$_SERVER['argv'][1] = 'env';
$_SERVER['argc'] = 2;
//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

define('LARAVEL_START', microtime(true));
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';

if (! isCli()) {
    exit('NOT CLI!');
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);
//$argv[1] = '111';
echo "<br/>\nCU = ";
echo getCurrentUserId();
echo "<br/>\n";
$mm = \App\Models\User::all();
foreach ($mm as $item) {
    echo "<br>\n $item->id, $item->email";
}

getch(' Enter to continue... ');
if (isCli()) {
    echo "\n CLI ";
}

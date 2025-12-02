<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v3.mytree.vn';

require_once __DIR__.'/../../index.php';


//Get connection DB:
$connection = DB::connection($connectionName);
$driver = $connection->getDriverName();
echo "<br/>\n  $driver";

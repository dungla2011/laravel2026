<?php
use App\Models\SiteMng;
//require_once "/var/www/html/public/index.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if($table = request('table')){
    $sid = SiteMng::isUseOwnMetaTable();
    $tmpFile = sys_get_temp_dir()."/glx_web/$sid-glx_cache_meta_api-$table";
    if(file_exists($tmpFile))
        unlink($tmpFile);
    echo "_success_delete_cache_meta_";
    return;
}

echo "_error_delete_cache_meta_";
return;

<?php

use App\Models\SiteMng;

require_once "/var/www/html/public/index.php";
if($table = request('table')){
    $sid = SiteMng::isUseOwnMetaTable();
    $tmpFile = sys_get_temp_dir()."/glx_web/$sid-glx_cache_meta_api-$table";
    if(file_exists($tmpFile))
        unlink($tmpFile);
}

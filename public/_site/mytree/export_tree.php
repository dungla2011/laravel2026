<?php

//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
use App\Models\SiteMng;
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$objPr = new \App\Components\clsParamRequestEx();
$objPr->set_gid = 3;


//$objPr->need_set_uid = $objTree->user_id;

$pid = 11461492867596288;
if($x = request('pidx')){
    $pid = $x;
}

$mretAll = [];

\App\Models\GiaPha_Meta::getTreeDeep($pid, $objPr, $mretAll, 0);

foreach ($mretAll AS &$one){
    if($one['child_type'] ?? ''){
        if($one['child_type'])
            $one['parent_id'] = null;
    }
    if($one['parent_id'] == 0)
        $one['parent_id'] = null;
    if($tmp = \App\Models\GiaPha::find($one['id'])){
        $one['content'] = $tmp['content'];
    }
}

ob_clean();
echo json_encode($mretAll, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mretAll);
//echo "</pre>";

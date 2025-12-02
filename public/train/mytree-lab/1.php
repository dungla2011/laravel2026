<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';
require "/var/www/html/public/index.php";

//$pid = qqgetIdFromRand_('js156958');
$pid = 11461493758623744;
//$pid = qqgetIdFromRand_('zc327648');

$obj = new \App\Models\GiaPha();
$mm = $obj->getAllTreeDeepCTE($pid, "id, name,title,birthday, image_list, orders, parent_id, married_with, home_address, created_at, updated_at, gender, child_of_second_married, _image_list, phone_number, email_address, date_of_death, place_heaven, stepchild_of: null, has_child");
//$mm = $obj->getAllTreeDeepCTE($pid, "*");
//$mm = $obj->getAllTreeDeepCTE($pid);
//$mm = \App\Models\GiaPha::getTreeCTE($pid, "id, name, parent_id, married_with");

$m1 = [];
foreach ($mm as $obj) {
    $obj = (object)$obj;
//    $obj->pid = $obj->parent_id;
    $obj->name = \LadLib\Common\cstring2::convert_codau_khong_dau($obj->name);
//    $obj->spouse_with = $obj->married_with;

    if ($obj1 = \App\Models\GiaPha::find($obj->id)) {
//        echo "<br/>\n";
        if ($obj1 instanceof \App\Models\GiaPha) ;
        $linkimg = "https://mytree.vn/" . $obj1->getThumbInImageList();
        $obj->img_link = $linkimg;
//        echo "<br/>\n $linkimg";
    }

    $m1[] = $obj;
}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($m1);
//echo "</pre>";


//echo "<br/>\n";
echo json_encode($m1, JSON_PRETTY_PRINT);

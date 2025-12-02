<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

//$_SERVER['SERVER_NAME'] = '';
require_once "/var/www/html/public/index.php";

if(!$uid = getCurrentUserId()){
    die("NOT LOGIN?");
}

if(!$idf = request('idf')){
    die("NOT VALID ID?");
}
$idf0 = $idf;
//if(is_numeric($idf)){
//    die("NOT VALID IDF3?");
//}

$idf = qqgetIdFromRand_( $idf );

if(!$gp = \App\Models\GiaPha::find($idf)){
    die("NOT FOUND IDF? $idf0");
}

if($gp->married_with){
    $idf0 = $idf = $gp->married_with;
    if(!$gp = \App\Models\GiaPha::find($idf)){
        die("NOT FOUND IDF2?");
    }
}

//if(isDebugIp()){
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($gp->toArray());
//    echo "</pre>";
//    die();
//}


if(!isSupperAdmin_())

if($gp->user_id != $uid){
    die("Không phải dữ liệu của bạn?");
}

$id0 = $id = qqgetIdFromRand_($idf);
//$id0 = 2597;

//Lay ra parent_id cua id nay
$pid0 = \App\Models\GiaPha::find($id)->parent_id;

//die(" $idf0 $id0, $pid0");

//echo "<br/>\n ID = $id0";

$gp = new \App\Models\GiaPha();

$mm = $gp->getAllTreeDeepCTE($id0, "id, parent_id, name, married_with, gender");

function escapeXmlSpecialChars($string) {
    $search = ['&', '<', '>', '"', "'"];
    $replace = ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'];
    return str_replace($search, $replace, $string);
}

//Chuyen het name tu tieng Viet sang tieng viet khong dau
foreach ($mm as $k => $v) {
//    $mm[$k]['name'] = \LadLib\Common\cstring2::convert_codau_khong_dau($v['name']);
    //Tìm các elm có married_with = $id0, thì gán parent_id = 0
    if ($v['married_with'] == $id0) {
        $mm[$k]['parent_id'] = $pid0;
    }

    if($mm[$k]['parent_id'] == $pid0)
        $mm[$k]['parent_id'] = 0;

    $mm[$k]['name'] = escapeXmlSpecialChars($v['name']);
    $mm[$k]['name'] = str_replace(":", " - ", $mm[$k]['name']);

}


$mm[] = ['id' => 0, 'parent_id' => null, 'married_with' => null, 'gender' => 1, 'name' => 'Root'];
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mm);
//echo "</pre>";
echo json_encode($mm, JSON_PRETTY_PRINT);
return;

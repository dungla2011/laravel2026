<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../index.php';

//https://github.com/daohoangson/dvhcvn
$file = public_path().'/dvhcvn.json';
$cont = file_get_contents($file);
$cont = str_replace('level1_id', 'id', $cont);
$cont = str_replace('level2_id', 'id', $cont);
$cont = str_replace('level3_id', 'id', $cont);
$cont = str_replace('level3s', 'child', $cont);
$cont = str_replace('level2s', 'child', $cont);
$cont = str_replace('level1s', 'child', $cont);

$mm = json_decode($cont);

$m1 = $mm->data;

$lv = 0;
function insertData($m1, $pr, $lv)
{
    if ($m1) {
        foreach ($m1 as $obj) {
            $x1 = clone $obj;
            unset($x1->child);
            echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
            print_r($x1);
            echo '</pre>';
            echo "<br/>\n LV: $lv - Insert $obj->name  / $pr";
            if ($dv = \App\Models\DonViHanhChinh::where('code', $obj->id)->first()) {
                echo "<br/>\n Đã insert..";
            } else {
                $dv = new \App\Models\DonViHanhChinh();
                $dv->name = $obj->name;
                $dv->type = $obj->type;
                $dv->parent_id = $pr;
                $dv->code = $obj->id;
                $dv->save();
            }

            $lastId = $dv->id;
            echo "<br/>\n lastId = $lastId";
            if (isset($obj->child)) {
                insertData($obj->child, $lastId, $lv + 1);
            }
        }
    }
}

insertData($m1, 0, 0);

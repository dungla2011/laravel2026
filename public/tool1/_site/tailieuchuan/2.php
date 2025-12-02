<?php


use App\Models\FileUpload;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';


require_once "/var/www/html/public/index.php";

if(!isCli()){
    die(" NOT CLI!");
}


$mfile = FileUpload::where("refer", 'LIKE', "idx=%")->get();

$midx = [];
foreach ($mfile AS $f1) {
    $idx = str_replace("idx=", "", $f1->refer);
//    echo "<br/>\n --- $f1->id --> $idx , $f1->refer, $f1->name";

    $midx[$idx] = $f1->id;
}
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($midx);
//echo "</pre>";
//return;


$mq = \App\Models\QuizQuestion::all();

foreach ($mq AS $item) {
    echo "<br/>\n --- IDQ = $item->id, $item->name";
    echo "<br/>\n $item->image_list, $item->created_at";

    $change = 0;
    if (isset($midx[$item->image_list])) {
        $oldId = $item->image_list;
        $newId = $midx[$item->image_list];
        //So sanh 2 file neu giong nhau thi bo qua
        $f1 = \App\Models\FileUpload::find($newId);
        $f2 = \App\Models\FileUpload::find($oldId);
        if ($f1->cloud_id == $f2->cloud_id) {
            echo "<br/>\n Not change: $f1->id, $f1->name";
//            continue;
        } else {
            $change = 1;
            echo "<br/>\n Will change...";
        }

        echo "<br/>\n Change From $item->image_list -> " . $midx[$item->image_list];
        $item->image_list = $newId;
    } else {
        $m2 = explode(",", $item->image_list);
        $list2 = '';

        foreach ($m2 as $id2) {
            if (isset($midx[$id2])) {
                $list2 .= $midx[$id2] . ',';

                $f1 = \App\Models\FileUpload::find($id2);
                $f2 = \App\Models\FileUpload::find($midx[$id2]);
                if ($f1->cloud_id == $f2->cloud_id) {
                    echo "<br/>\n Not change: $f1->id, $f1->name";
//            continue;
                } else {
                    echo "<br/>\n Will change...";
                    $change = 1;
                }

            }
        }
        $list2 = rtrim($list2, ',');
        echo "<br/>\n Not change: $list2";
        $item->image_list = $list2;
    }

    if ($change) {
        echo "<br/>\n Change now";
//        getch("  change now ... ");
        $item->save();
//        getch(" after change ... ");
    }
    else{
//        getch(" not change ... ");
    }





//    if(isset($midx[$item->image_list]))
//        $item->image_list = $midx[$item->image_list];



//    $m1 = \App\Models\FileUpload::whereIn("id", explode(',',$item->image_list))->get();
//    foreach ($m1 AS $file){
//        echo "<br/>\n File OK: $file->id, $file->name";
//    }

}

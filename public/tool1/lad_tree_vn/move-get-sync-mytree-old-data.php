<?php

/**
 * Copy dữ liệu từ site cũ sang site mới
 php /var/www/html/public/tool1/lad_tree_vn/get-sync-mytree-old-data.php
 */
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require_once __DIR__.'/../../index.php';

$dbX = \App\Components\Helper1::getDBInfo();
echo "\n Dbname : ".$dbX['database'].' / '.$dbX['host'];

if (! isCli()) {
    exit('<br>NOT CLI!');
}

$isInsertUpdate = 0;

$y = getch('Insert (Or view only), y to insert :');
if ($y == 'y') {
    $isInsertUpdate = 1;
}

//hoanhai131091@gmail.com

$email0 = 'hoanhai131091@gmail.com';

//$email = 'ongminh54@gmail.com';
//$email = 'btlang2011@gmail.com';
//$email = 'dquy6686@gmail.com';
//$email ='nc9789104@gmail.com';
//$email = 'tuanlehuu77@gmail.com';
//$email = 'dungbkhn02@gmail.com';
$email = 'hoanhai131091@gmail.com';

//Email0, là mail trên mytree
$email0 = $email;

$user = \App\Models\User::where('email', "$email0")->first();
if (! $user) {
    exit("Not user? $email");
}

$urlBase = env('APP_URL');
$urlBase = "https://". $_SERVER['HTTP_HOST'];

echo "<br>URL BASE = $urlBase , TK = ".$user->getUserToken();

//$user = auth()->user();
//if(!$user){
//    die("Need login!");
//}

//$email = $user->email;

echo "<br/>\n Email = $user->email / $email0";

$uid = $user->id;

$url = "https://giapha.galaxycloud.vn/train/_learn_html_css_js/svg%20train/get-data-from-giapha.php?get-list-of-user=$email";

$ct = file_get_contents($url);

$mm = json_decode($ct);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mm);
//echo "</pre>";
//
//return;

foreach ($mm as $pid => $enc) {

    $url1 = "https://giapha.galaxycloud.vn/train/_learn_html_css_js/svg%20train/get-data-from-giapha.php?pid=$pid&include_brother=1";

    if (isSupperAdmin__()) {

        echo "<br/>\n --- URL = $url1 ";
    }

    $ct1 = file_get_contents($url1);

    $m1 = json_decode($ct1);
    //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //    print_r($ct1);
    //    echo "</pre>";

    $tt = count($m1);
    $cc = 0;
    foreach ($m1 as $elm) {
        $cc++;
        echo "<br/>\n OBJ = $cc/$tt ID = $elm->id . $elm->name";
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($elm);
        //        echo "</pre>";

        $imgIdUpload = null;
        $objUpdate = \App\Models\GiaPha::where('tmp_old_id', $elm->id)->first();

        $tk = $user->getUserToken();

        if ($objUpdate) {
            if (! $objUpdate->image_list) {
                echo "<br/>\n OBJID  = $objUpdate->id,  $objUpdate->name / IDE = ".qqgetRandFromId_($objUpdate->id);

                if (isCli()) {
                    if (isset($elm->img)) {
                        echo "<br/>\n IMG remote = $elm->img";
                        $img = $elm->img;
                        if (str_starts_with($img, 'https://')) {

                            //                    getch("...1 , TK = $tk");
                            echo "<br/>\n --- Download now: $img ";
                            $ctx = stream_context_create(['http' => [
                                'timeout' => 60,  //1200 Seconds is 20 Minutes
                            ],
                            ]);
                            $imgCont = file_get_contents($img, false, $ctx);

                            $urlUpload = "$urlBase/api/member-file/upload";

                            echo "<br/>\n Upload url: $urlUpload";

                            if ($idf = \App\Models\FileUpload::uploadFileContentByApi($urlUpload, $user->getUserToken(), $imgCont, 'file_name_')) {
                                echo "\n update id objUpdate->image_list = $idf;";
                                $objUpdate->image_list = $idf;
                                if ($isInsertUpdate) {
                                    $objUpdate->update();
                                }
                                //                        getch("update img id : $ofile->id / ID GP: $objUpdate->id");
                            } else {
                                getch('Error, can not upload?');
                            }

                            //                    getch("...2");
                        }
                    }
                }
            } else {
                echo "\n objUpdate->image_list = $objUpdate->image_list";
            }
        }

        if (! isset($elm->married_with)) {
            $elm->married_with = null;
        }
        if (! isset($elm->birthday)) {
            $elm->birthday = null;
        }

        $elm->birthday = substr($elm->birthday, 0,30);

        if (! isset($elm->orders)) {
            $elm->orders = null;
        }
        if (! ($elm->orders)) {
            $elm->orders = 0;
        }
        if ($elm->orders > 1000000) {
            $elm->orders = 1000000;
        }
        if (! isset($elm->child_of_second_married)) {
            $elm->child_of_second_married = null;
        }
        if (! ($elm->child_of_second_married)) {
            $elm->child_of_second_married = null;
        }
        if (! isset($elm->status)) {
            $elm->status = null;
        }
        if (! isset($elm->gender)) {
            $elm->gender = null;
        }
        if (! isset($elm->child_type)) {
            $elm->child_type = null;
        }
        $elm->image_list = $imgIdUpload;

        $minput = [
            'tmp_old_id' => $elm->id,
            'tmp_old_pid' => $elm->parent_id,
            'user_id' => $uid,
            'image_list' => $imgIdUpload,
            'parent_id' => $elm->parent_id,
            'name' => $elm->name,
            'married_with' => $elm->married_with,
            'birthday' => $elm->birthday,
            'orders' => $elm->orders,
            'child_of_second_married' => $elm->child_of_second_married,
            'status' => $elm->status,
            'gender' => $elm->gender,
            'child_type' => $elm->child_type,
            //            'sur_name' => $elm->id,
            //            'last_name' => $elm->id,
            'tmp_old_obj_json' => json_encode($elm),
        ];

        echo "<br/>\n === $elm->id";
        if (! $objUpdate) {
            echo "<br/>\n Chưa insert";
            if ($isInsertUpdate) {
                \App\Models\GiaPha::create($minput);
            }
        } else {
            echo "<br/>\n ID = $objUpdate->id. Đã insert ($objUpdate->name)";
            if ($objUpdate->child_of_second_married) {
                echo "<br/>\n child_of_second_married = $objUpdate->child_of_second_married";
            }
        }
    }
    //    break;
}

$mm = \App\Models\GiaPha::where('user_id', $uid)->where('tmp_old_id', '>', 0)->get();
if ($mm) {
    foreach ($mm as $obj) {

        $oldObj = json_decode($obj->tmp_old_obj_json);
        //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //    print_r($oldObj);
        //    echo "</pre>";

        //Nếu thấy pid, và pid =
        if ($obj->parent_id && $obj->parent_id == $obj->tmp_old_pid) {
            //Tìm đối tượng cũ để update lại parent
            if ($objPr = \App\Models\GiaPha::where('tmp_old_id', $obj->parent_id)->first()) {
                $obj->parent_id = $objPr->id;
                if ($isInsertUpdate) {
                    $obj->update();
                }
                echo "<br/>\n $obj->name ($obj->id):  Update parent to = $objPr->id";
            } else {
                echo "<br/>\n $obj->name ($obj->id):  Update parent to = 0";
                $obj->parent_id = 0;
                if ($isInsertUpdate) {
                    $obj->update();
                }
            }
        }
        //VC:

        if ($obj->married_with && $obj->married_with == $oldObj->married_with) {
            echo "<br/>\n$obj->id, Need update married_with";
            if ($objMr = \App\Models\GiaPha::where('tmp_old_id', $obj->married_with)->first()) {
                $obj->married_with = $objMr->id;
                if ($isInsertUpdate) {
                    $obj->update();
                }
                echo "<br/>\n $obj->name ($obj->id):  Update married_with to = $objMr->id";
            } else {
                $obj->married_with = 0;
                if ($isInsertUpdate) {
                    $obj->update();
                }
            }
        }

        if ($obj->child_of_second_married && $obj->child_of_second_married == $oldObj->child_of_second_married) {
            echo "<br/>\n$obj->id, Need update child_of_second_married";
            if ($objMr = \App\Models\GiaPha::where('tmp_old_id', $obj->child_of_second_married)->first()) {
                $obj->child_of_second_married = $objMr->id;
                if ($isInsertUpdate) {
                    $obj->update();
                }
                echo "<br/>\n $obj->name ($obj->id):  Update child_of_second_married to = $objMr->id";
            } else {
                $obj->child_of_second_married = 0;
                if ($isInsertUpdate) {
                    $obj->update();
                }
            }
        }

    }
}

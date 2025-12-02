<?php

/**
 * Nếu đã clone rồi thì clone lại thì sao????
 */

require_once '../../index.php';

$idR = $id = 'cq236850';

$id = qqgetIdFromRand_($id);

$obj = new \App\Models\GiaPha();

$obj = new \App\Models\GiaPha();
$mm = $obj->getAllTreeDeep($id);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mm);
//echo "</pre>";
//
//return;
//$user = \App\Models\User::where("email", 'mytreevn2015@gmail.com')->first();
$userNew = \App\Models\User::where('email', 'sonnguyen1079@gmail.com')->first();
$userNew = \App\Models\User::where('email', 'dungla2011@gmail.com')->first();
$uidnew = $userNew->id;

//Tìm xem gia pha có UID cũ đó không:

foreach ($mm as $elm) {
    echo "<br/>\n {$elm['id']} / {$elm['name']}";

    //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //    print_r($elm);
    //    echo "</pre>";
    //
    //    continue;

    $idOK = $elm['id'];

    echo "<br/>\n ID = ".$idOK;

    if ($obj = \App\Models\GiaPha::where(['user_id' => $uidnew, 'tmp_old_id' => $idOK])->first()) {
        //        $obj->forceDelete();

        //        $obj->image_list = '';
        //        $obj->update();

        echo "<br/>\n Đã insert cont...";

        continue;
    }

    echo "<br/>\n Insert now:";
    $m1 = $elm;
    $m1['tmp_old_obj_json'] = serialize($elm);
    $m1['tmp_old_id'] = $idOK;
    $m1['user_id'] = $uidnew;
    unset($m1['id']);
    unset($m1['created_at']);
    unset($m1['updated_at']);
    unset($m1['deleted_at']);
    //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //    print_r($m1);
    //    echo "</pre>";
    \App\Models\GiaPha::create($m1);
}

//return;

$cc = 0;
$m2 = \App\Models\GiaPha::where(['user_id' => $uidnew])->where('tmp_old_id', '>', 0)->get();
if ($m2) {
    echo "<br/>\n Check Update  m2: ".count($m2);
    foreach ($m2 as $obj) {
        $cc++;
        echo "<br/>\n $cc. Check Update: $obj->name ";
        if (! $obj->tmp_old_id) {
            continue;
        }

        //Tìm Thằng có PID trong  này
        foreach ($m2 as $obj1) {
            if ($obj1->parent_id == $obj->tmp_old_id) {
                //Thì gán lại pid cho obj1
                $obj1->parent_id = $obj->id;
                $obj1->update();
                echo "<br/>\n Update parent OK!";
            }
            if ($obj1->married_with == $obj->tmp_old_id) {
                //Thì gán lại pid cho obj1
                $obj1->married_with = $obj->id;
                $obj1->update();
                echo "<br/>\n Update married_with OK!";
            }
            if ($obj1->child_of_second_married == $obj->tmp_old_id) {
                //Thì gán lại pid cho obj1
                $obj1->child_of_second_married = $obj->id;
                $obj1->update();
                echo "<br/>\n Update child_of_second_married OK!";
            }
        }
    }

}

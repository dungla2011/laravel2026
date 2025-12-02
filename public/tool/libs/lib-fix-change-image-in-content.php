<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Models\CloudServer;
use Illuminate\Support\Carbon;

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = ('tailieuchuan.net');

require_once '/var/www/html/public/index.php';

die("xxx");

$mm = \App\Models\QuizQuestion::all();

$fix = 0;
foreach ($mm as $m) {

//    echo $m->content;
    if(strstr($m->content, 'test_cloud_file?fid=')) {
        echo "\n<br> $m->id <br>";

        $fix++;
        $ct = $m->content;

        //Lấy ra các file_id sau fid=
        $x = str_get_html($ct);
        foreach ($mImg = $x->find('img') AS $img){
            $src = $img->src;
            if(strstr($src, 'test_cloud_file?fid=')) {
                $fid = substr($src, strpos($src, 'fid=')+4);
                if(is_numeric($fid)){
                    echo "\n $src -> $fid";
                    //Tim ra fileid  nay trong FileUpload
                    if($f = \App\Models\FileUpload::find($fid)){
                        echo " -> IDE  = $f->ide__";
//                        getch("...1");
                        $ct = str_replace("test_cloud_file?fid=$fid", "test_cloud_file?fid=$f->ide__", $ct);
                        $m->content = $ct;
                        $m->addLog("change file id $fid to $f->ide__");
//                        $m->save();
//                        getch("...2");
                    }
                }

            }
        }

//        echo "\n $ct";
    }

}

echo "\n ---- $fix";


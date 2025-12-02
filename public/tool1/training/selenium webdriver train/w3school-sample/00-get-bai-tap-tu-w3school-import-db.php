<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'misu.mytree.vn';

require_once '/var/www/html/public/index.php';

if (isWindow1()) {
    echo "\n php /var/www/html/public/tool1/training/selenium\ webdriver\ train/w3school-sample/00-get-bai-tap-tu-w3school-import-db.php";
    exit('Need run on linux! ');
}

//require_once 'qA_W3School.php';
require_once '/var/www/html/app/Components/qA_W3School.php';
require_once '/var/www/html/vendor/_ex/simple_html_dom.php';

$updateAns = 0;
$updateQues = 0;
$updateContentText = 0;

$url = 'https://www.w3schools.com/css/exercise.asp';
$url = 'https://www.w3schools.com/html/exercise.asp';
$url = 'https://www.w3schools.com/python/exercise.asp';
$url = 'https://www.w3schools.com/java/exercise.asp';
$url = 'https://www.w3schools.com/mysql/exercise.asp';
$url = 'https://www.w3schools.com/php/exercise.asp';
$url = 'https://www.w3schools.com/js/exercise_js.asp';
$url = 'https://www.w3schools.com/css/exercise.asp';

$x0 = file_get_html($url);
$str = $x0->find('.exercisemenuinner', 0)->innertext;
$fold = trim(explode('/', explode('w3schools.com/', $url)[1])[0]);

$fold = strtoupper($fold);
if (! $objF = \App\Models\QuizFolder::where(['name' => $fold, 'parent_id' => 3])->first()) {
    $objF = new \App\Models\QuizFolder();
    $objF->parent_id = 3;
    $objF->name = ($fold);
    $objF->refer = $url;
    $objF->save();
    //    getch('...' . $objF->id);
}

$pid0 = $objF->id;

//echo "<br/>\n  $fold";
//return;

$xx = str_get_html($str);

$baiSo = 0;
$cc = 0;
foreach ($xx->find('.exmenuitemheader') as $d) {
    $lesson = trim(strip_tags($d->innertext));
    $baiSo++;
    echo "<br/>\n $lesson";

    if (! $objF = \App\Models\QuizFolder::where(['name' => $lesson, 'parent_id' => $pid0])->first()) {
        $objF = new \App\Models\QuizFolder();
        $objF->parent_id = $pid0;
        $objF->name = ($lesson);
        $objF->save();
        //    getch('...' . $objF->id);
    }

    $pid1 = $objF->getId();

    $next = $d->next_sibling();

    $bai0 = 0;
    foreach ($next->find('a') as $a1) {
        if ($a1->target == '_blank') {
            continue;
        }
        $cc++;
        $bai0++;
        $link = "https://www.w3schools.com/$fold/$a1->href";

        echo "<br/>\n $cc. LINK = $link ";
        if ($qdb = \App\Models\QuizQuestion::where(['refer' => $link])->first()) {

            $html = file_get_content_cache($link);
            $qa = new qA_W3School($html);
            if ($updateQues) {
                if ($qa->questionText != $qdb->content) {
                    echo "<br/>\n $qa->questionText ";
                    echo "<br/>\n $qdb->content ";
                    //                getch("...update $qdb->id ");
                    $qdb->content = $qa->questionText;
                    $qdb->update();

                    continue;
                }
            }

            $answerInHtml = implode("\n", $qa->answerArray);
            if ($updateAns) {
                if ($answerInHtml != $qdb->answer) {
                    //                print_r($qa->answerArray);
                    echo "\n $answerInHtml != $qdb->answer";
                    //                getch("...update answ: $qdb->id ");
                    $qdb->answer = $answerInHtml;
                    $qdb->update();

                    continue;
                }
            }

            if ($qdb->parent_id != $pid1) {
                $qdb->parent_id = $pid1;
                $qdb->update();
            }

            $qdb->content_textarea = trim($qdb->content_textarea);

            if ($updateContentText) {
                if (! $qdb->content_textarea) {
                    getch("... $qdb->id ");
                    $qdb->content_textarea = json_decode($qdb->obj_refer)->questionCont;
                    $qdb->update();
                    getch('...');
                }
            }

            continue;
        }

        $cauHoi = trim(strip_tags($a1->innertext));
        echo "<br/>\n *** $cc. $cauHoi | $link  ";
        //        echo "<br/>\n <a href='$link'> LINK </a>";

        $b00 = sprintf('%02d', $baiSo);
        $name = $b00.'.'.$bai0.'-'.$lesson."-$cauHoi";
        $name = str_replace(' ', '_', $name);
        $name = str_replace('\\', '_', $name);
        $name = str_replace('/', '_', $name);

        echo "<br/>---- NAME = $name";

        $html = file_get_content_cache($link);
        $qa = new qA_W3School($html);

        if ($qa->answerArray) {

            //            echo "<br/>\n $qa->questionText ";

            if (! \App\Models\QuizQuestion::where(['refer' => $link])->first()) {
                $qdb = new \App\Models\QuizQuestion();
                $qdb->name = $name;
                $qdb->content = $qa->questionText;
                $qdb->refer = $link;
                $qdb->parent_id = $pid1;
                $qdb->obj_refer = json_encode($qa);
                $qdb->content_textarea = $qa->questionCont;
                $qdb->answer = implode("\n", $qa->answerArray);
                $qdb->type = 3;
                $qdb->save();
                //            getch("...");
            }
        }

    }
}

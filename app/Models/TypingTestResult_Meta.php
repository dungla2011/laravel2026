<?php

namespace App\Models;

use LadLib\Common\clsDateTime2;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class TypingTestResult_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/typing-test-result';

    protected static $web_url_admin = '/admin/typing-test-result';

    protected static $api_url_member = '/api/member-typing-test-result';

    protected static $web_url_member = '/member/typing-test-result';

    //public static $folderParentClass = TypingTestResultFolderTbl::class;
    public static $modelClass = TypingTestResult::class;

    public static $disableAddItem = 1;

    public static $disableSaveAllButton = 1;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //TypingTestResult edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function getKetQuaHtml($uid, $numberLast = 10)
    {

        $mL = TypingLesson::all();

        echo "\n<table data-code-pos='ppp16949213813801' class='table table-bordered'>";
        echo "\n <tr> <th>Bài số</th> <th>Tên bài</th> <th>Kết quả cao nhất</th>  <th> Tổng hợp </th> </tr>";
        foreach ($mL as $ls) {

            $rs = TypingTestResult::where(['user_id' => $uid, 'lesson' => $ls->id])->orderBy('accuracy', 'desc')->first();
            $max = '';

            if ($rs) {

                $st = '';
                if ($rs->accuracy == 100) {
                    $st = ';color: blue;';
                }

                $nday = round((time() - strtotime($rs->created_at)) / 84600);
                if ($nday) {
                    $nday = " $nday ngày trước, ";
                } else {
                    $nday = ' Hôm nay, ';
                }
                $dateVN = clsDateTime2::showDateTimeStringVNToDisplay(strtotime($rs->created_at));

                $max = " Độ chính xác: <b style='$st'>$rs->accuracy % </b>  <br>Tốc độ: $rs->speedc (ký tự/phút) , $rs->speedw (từ/phút) <br> <i style='font-size: x-small'>$nday $dateVN</i> ";
            }

            $count = TypingTestResult::where(['user_id' => $uid, 'lesson' => $ls->id])->count();

            echo "\n <tr data-code-pos='ppp16949229870471' data-code-pos='ppp16949213852191'>";
            echo "\n <td style='text-align: center'>";
            echo "\n Bài số $ls->id";
            echo "\n </td>";
            echo "\n <td>";
            echo "\n  $ls->parent_name - $ls->name <br> <a data-code-pos='ppp16949213765901' href='/?fid=$ls->id'>Luyện tập</a>  ";
            echo "\n </td>";
            echo "\n <td style='font-size: small'>  $max ";
            echo "\n </td> ";
            echo "\n <td style='width: 200px'> Số lần tập: $count <br> <a target='_blank' href='/member/typing-test-result?seby_s13=$ls->id'>Lịch sử</a> </td> ";
            echo "\n </td> ";

            echo "\n </tr>";
        }
        echo "\n</table>";

        $html = '';
        $html = "\n <div style='background-color: #eee;' class='p-4 mb-3'> <h3> $numberLast lần tập cuối <a href='/member/typing-test-result' style='float: right'> Xem tất cả</a> </h3> <hr>";

        $mm = \App\Models\TypingTestResult::where('user_id', $uid)->orderBy('created_at', 'DESC')->limit($numberLast)->get();

        if (! count($mm)) {
            return '<h2>Bạn chưa làm bài tập nào!</h2>';
        }

        $stt = 0;
        foreach ($mm as $obj) {
            if ($ls = \App\Models\TypingLesson::find($obj->lesson)) {
                $stt++;
                $ret = \App\Models\TypingTestResult_Meta::tinhDoChinhXac($ls->type_text, $obj);

                $doChinhXac = $ret['do_chinh_xac'];
                $word_per_min = $ret['word_per_min'];
                $char_per_min = $ret['char_per_min'];
                $dtime = $ret['dtime'];
                $total_word = $ret['total_word'];

                $padColor = '; color: blue; ';
                if ($doChinhXac < 90) {
                    $padColor = '; color: red; ';
                }

                $padColor2 = '; color: blue; ';
                if ($char_per_min < 250) {
                    $padColor2 = '; color: red; ';
                }
                if ($char_per_min > 150 && $char_per_min < 250) {
                    $padColor2 = '; color: green; ';
                }

                $endT = $obj->end_time;
                if (! $endT) {
                    $endT = '(Chưa kết thúc)';
                }

                $html .= "<div class='ket-qua-one kq$stt' data-stt='$stt'>Bài Tập: <b>  $ls->name </b> ".
                    "(Mã số : $ls->lesson)  <br>$total_word word | $dtime giây |  Độ chính xác:  <b style='$padColor'>$doChinhXac</b> % |".
                    " Tốc độ: <b> $word_per_min </b> từ/phút , <b style='$padColor2'>$char_per_min </b> ký tự / phút <br> <span style='font-size: small'> Thời gian bắt đầu: $obj->created_at, kết thúc: $endT </span> </div>";
                $html .= " <a class='btn btn-sm btn-default ' href='/?fid=$ls->id'> Tập gõ </a> <hr>";
                //echo "\n <br> 1. $obj->created_at,  $obj->type_text <br> 2.  $ls->type_text";
            }
        }

        $html .= "\n</div>";

        return $html;
    }

    public static function tinhDoChinhXac($strOrg, $objTest)
    {

        $strInput = trim($objTest->type_text);

        $dtime = strtotime($objTest->end_time) - strtotime($objTest->created_at);

        $m1 = explode('¶', $strOrg);
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($m1);
        //        echo "</pre>";
        $m2 = json_decode($strInput);
        $good = $bad = 0;

        $strAll = trim(str_replace('¶', ' ', $strOrg));
        $strAll = preg_replace('!\s+!', ' ', $strAll);
        $mAll = explode(' ', $strAll);
        $mAll = array_filter($mAll);

        $totalWord = count($mAll);

        $ttWInput = count(explode(' ', $strInput));
        //$word_per_min = round($totalWord / $dtime * 60);
        $word_per_min = round($ttWInput / $dtime * 60);
        $char_per_min = round(strlen($strInput) / $dtime * 60);

        //        echo "\n TotalW = $totalWord";

        for ($i = 0; $i < count($m1); $i++) {
            $l1 = $m1[$i];
            if (isset($m2[$i])) {
                $l2 = $m2[$i];
                $l1 = preg_replace('!\s+!', ' ', $l1);
                $l2 = preg_replace('!\s+!', ' ', $l2);

                $m11 = explode(' ', $l1);
                $m21 = explode(' ', $l2);

                for ($i1 = 0; $i1 < count($m11); $i1++) {
                    $tu1 = $m11[$i1];
                    if (! isset($m21[$i1])) {
                        continue;
                    }

                    $tu2 = $m21[$i1];
                    if ($tu1 == $tu2) {
                        $good++;
                    } else {
                        $bad++;
                    }

                }
            }

        }

        if ($totalWord == 0) {
            return 0;
        }
        $doChinhXac = number_format($good / $totalWord * 100, 1);

        if ($strInput && ! $objTest->speedc) {
            if ($objUpdate = TypingTestResult::find($objTest->id)) {
                $objUpdate->accuracy = $doChinhXac;
                $objUpdate->speedw = $word_per_min;
                $objUpdate->speedc = $char_per_min;
                $objUpdate->save();

            }
        }

        return ['do_chinh_xac' => $doChinhXac, 'total_word' => $ttWInput, 'dtime' => $dtime, 'word_per_min' => $word_per_min, 'char_per_min' => $char_per_min];
    }

    public function _type_text($obj, $val, $field)
    {

        $ls = $obj->lesson;

        $tl = TypingLesson::find($ls);

        $ret = self::tinhDoChinhXac($tl->type_text, $obj);

        $strAll = trim(str_replace('¶', ' ', $tl->type_text));
        $strAll = preg_replace('!\s+!', ' ', $strAll);
        $mAll = explode(' ', $strAll);
        $mAll = array_filter($mAll);

        $totalWord = count($mAll);

        $doChinhXac = $ret['do_chinh_xac'];
        $word_per_min = $ret['word_per_min'];
        $char_per_min = $ret['char_per_min'];
        $dtime = $ret['dtime'];
        $total_word = $ret['total_word'];

        return "<div style='font-size: small; padding: 10px'> Bài Tập: <b>  $tl->name </b> (Mã số : $tl->lesson)  <br>$total_word word | $dtime giây |  Độ chính xác:  <b>$doChinhXac</b> % | Tốc độ: <b> $word_per_min </b> từ/phút , <b>$char_per_min </b> ký tự / phút <br> <span style='font-size: small'> $obj->created_at </span> </div>";

        //        echo "\n TotalW = $totalWord";
        //return " Độ chính xác: $doChinhXac % (Tổng số $totalWord word) ";
    }

    public function extraJsInclude()
    {
        ?>

        <style>
            input.input_value_to_post.readonly.type_text{
                display: none;
            }
        </style>
<?php
    }

    //...
}

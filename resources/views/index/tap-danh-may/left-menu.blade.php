<?php
$mm = \App\Models\TypingLesson::all();

$mL = [];
foreach ($mm AS $one) {
    if (!isset($mL[$one->parent_name])) {
        $mL[$one->parent_name] = [];
    }
    $mL[$one->parent_name][] = $one;

}

$c1 = $cc = 0;

$fid = $_GET['fid'] ?? 0;
$strTyping = '';

foreach ($mL AS $pname => $mSubLess) {

    $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('fid', $mSubLess[0]->lesson);
//        $link = "/?fid=" . $mSubLess[0]->lesson;
    echo "<ul data-id=''> <a href='$link'> <b> $pname </b> </a>  ";
    foreach ($mSubLess AS $s1) {
//        $link = "/?fid=" . $s1->lesson;
        $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('fid', $s1->lesson);
        $style = '';
        if ($fid == $s1->lesson) {
            echo "<span class='mark_selected' style='display: none'></span>";
            $style = "style='color: red'";
            $strTyping = $s1->type_text;
        }
        echo "\n  <li style='display: none'>  $s1->lesson. <a $style href='$link'> $s1->name  </a> </li>";
//            echo "<br/>\n Text: $s1->type_text";
    }


    echo "\n</ul>";

}
echo "\n <hr> <ul><a href='/member/typing-history' data-code-pos='ppp16884045341991'> <b>History </b> </a> </ul> ";
?>

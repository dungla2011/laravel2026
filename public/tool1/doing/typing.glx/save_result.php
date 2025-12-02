<?php

require_once '/var/www/html/public/index.php';

ob_start();

if (! $uid = getCurrentUserId()) {
    exit('<h2>Bạn cần đăng nhập để lưu lại kết quả đánh máy! </h2>');
}

$fid = request('fid');
$gsession = request('gsession');

if (! $fid || ! $gsession) {
    exit('Not valid param!');
}

if (request('start')) {

    if ($obj = \App\Models\TypingTestResult::where('user_id', $uid)->where('lesson', $fid)->where('gsession', $gsession)->first()) {
        return;
    }
    $obj = new \App\Models\TypingTestResult();
    $obj->user_id = $uid;
    $obj->gsession = $gsession;
    $obj->lesson = $fid;
    $obj->save();
    if ($obj->id) {
        exit('DONE Start!');
    }
}

if (request('done')) {
    $obj = new \App\Models\TypingTestResult();
    if (! $obj = \App\Models\TypingTestResult::where('user_id', $uid)->where('lesson', $fid)->where('gsession', $gsession)->first()) {
        exit('Not found session!');
    }

    $ls = \App\Models\TypingLesson::find($fid);
    if (! $ls) {
        echo _rtJsonApiError("Not found lesson: $fid");
    }

    if ($obj->end_time) {
        //        die("session đã cập nhật trước đó!");
    }

    $strType = [];
    for ($i = 0; $i < 1000; $i++) {
        if ($rq = request($i)) {
            $strType[] = $rq;
        }
    }

    if (! $obj->end_time) {
        $obj->type_text = json_encode($strType);
        $obj->end_time = nowyh();
        $obj->save();
    }
    if ($obj->id) {

        //Lấy N phần tử cuối ra để trả lại độ chính xác:
        $mm = \App\Models\TypingTestResult::limit(5)->latest()->get();
        $html = "<div class='ketqua'>";

        $html .= \App\Models\TypingTestResult_Meta::getKetQuaHtml($uid, 10);

        $html .= '</div>';
        ob_clean();
        echo $html;
        exit();
    }

    exit('Có lỗi: không lấy được kết quả!');
}

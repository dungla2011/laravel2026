<?php
use LadLib\Common\Database\MetaClassCommon;
use Stichoza\GoogleTranslate\GoogleTranslate;

$user = \Illuminate\Support\Facades\Auth::user();

if(!$user){
    bl("Not found user3?");
    return;
}

//Session ID
if($testIdOfUser = request('testIdOfUser')){
    if (!$objTestOfUser = \App\Models\QuizUserAndTest::find($testIdOfUser)) {
        return bl("Not found testid?");
    }

    //                echo "<br/>\ntestIdOfUser = $testIdOfUser / $session->name";

    $testId = $objTestOfUser->test_id;

    if (!$objTest = \App\Models\QuizTest::find($testId)) {
        return bl("Not found testid");
    }

    $sId = $objTestOfUser->session_id;
    if (!$objSession = \App\Models\QuizSessionInfoTest::find($sId)) {
        return bl("Not found sessionId");
    }
}
else
if($sId = request('sid')){
    if (!$objSession = \App\Models\QuizSessionInfoTest::find($sId)) {
        return bl("Not found sid");
    }

    if (!$objTestOfUser = \App\Models\QuizUserAndTest::where(['session_id'=>$sId, 'user_id'=> $user->getId()])->first()) {
        bl("Not found this test session?");
        goto _END;
    }
//    dump($objTestOfUser->toArray());
//    die();
    $testIdOfUser = $objTestOfUser->id;
    $testId = $objTestOfUser->test_id;
    if (!$objTest = \App\Models\QuizTest::find($testId)) {
        bl("Not found QuizTest id: $testId");
        goto _END;
    }
}
else{
    bl("Tham số không hợp lệ!");
    return;
}

require_once "../app/Components/qA_W3School.php";
require_once "../app/Components/simple_html_dom.php";




//$sObj = \App\Models\QuizSessionInfoTest::find($obj)

?>
@extends("layouts.member")

@section("title")
    Bài test: {{$objTest->name }}
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path() . '/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <style>
        .content-wrapper {
            background-color: white;
        }

        hr {
            height: 1px;
            border: 1px;
        }

        .qid {
            margin-right: 2px;
            background-color: dodgerblue;
            color: white;
            display: inline-block;
            padding: 2px 2px;
            font-size: small;
            min-width: 23px;
            text-align: center;
            margin-bottom: 10px;
            /*border-radius: 20%;*/
        }

        .done {
            background-color: darkgrey;
            color: #eee
        }

        .doing {
            /*background-color: orangered;*/
            border: 2px solid red;
            color: #eee;
        }

        .adm_zone a {
            color: transparent;
            background-color: transparent;

        }

        .topban {
            border: 1px solid #ccc;
            padding: 15px;
        }

        .pos1, .pos2 {
            background-color: #eee;
        }
        .pos2 p {
            margin-bottom: 5px;
            /*font-size: 90% !important;*/

        }
        .pos3 {
            background-color: #eee;
        }

     .unselectable {
         -webkit-user-select: none;
         -webkit-touch-callout: none;
         -moz-user-select: none;
         -ms-user-select: none;
         user-select: none;
         color: #cc0000;
     }

    </style>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })
    </script>
@endsection

@section("content")


    <div class="content-wrapper" data-code-pos="ppp1682133057970">
        <!-- Content Header (Page header) -->
        <div class="content-header">

            <div class="row mx-2">
                <div class="col-sm-4 topban pos1" style="">
            <h4 class=""> <i class="fa fa-check-circle" style="font-size: 70%"></i> <?php if ($objSession) echo $objSession->name ?>
            <?php

//                if(isSupperAdmin_())
                {
                    echo "\n <a href='/admin/quiz-user-and-test/edit/$testIdOfUser' target='_blank'> * </a>";
                }

            ?>
            </h4>

            <b class="">Bài test: <?php echo  "$objTest->name" ?></b>
{{--                    <br>Tạo lúc:--}}
                    <?php
//            echo substr($objSession->created_at, 0,16)
                    ?>
                </div>
                <div class="col-sm-8 topban pos2" style="">
                <?php

                $dMin = round((strtotime($objSession->end_time_do) - strtotime($objSession->start_time_do)) / 60);
                $dH = number_format($dMin/60,2);
                echo (" <p> <i class='fa fa-clock' style='font-size: 90%'></i> Thời gian làm bài: $objSession->start_time_do | $objSession->end_time_do ($dMin phút = $dH giờ) </p>");

                if(\App\Models\QuizTool::hetTimeLamBai($objSession)) {
                    echored(" <p><i class='fa fa-clock' style='font-size: 90%'></i> Đã hết thời gian làm bài! </p>");
                }
                echo ("<p><i class='fa fa-clock' style='font-size: 90%'></i> Thời gian mở đáp án: $objSession->open_answer_time | $objSession->close_answer_time </p>");
                ?>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <?php
//            bl("TIME = " . $objSession->start_time_do);

            if($objSession->start_time_do > nowyh()){
                echo "<br/>\n";echo "<br/>\n";echo "<br/>\n";
                bl("Bài Kiểm tra được mở lúc: " . nowyh_vn(strtotime($objSession->start_time_do)) );
                goto _END1;
            }
        ?>

        <!-- Main content -->
        <section class="content" data-code-pos="ppp1682133061076">
            <div class="container-fluid">

                {{--                <div class="row">--}}
                {{--                    <div class="col-sm-12 jumbotron">--}}

                {{--                    </div>--}}
                {{--                </div>--}}

                <?php



                //Session ID
//                $testIdOfUser = request('testIdOfUser');
//                if (!$objTestOfUser = \App\Models\QuizUserAndTest::find($testIdOfUser)) {
//                    bl("Not found this test session?");
//                    goto _END;
//                }
//                //                echo "<br/>\ntestIdOfUser = $testIdOfUser / $session->name";
//                $testId = $objTestOfUser->test_id;
//                if (!$objTest = \App\Models\QuizTest::find($testId)) {
//                    bl("Not found this test id?");
//                    goto _END;
//                }
                $resultUser = null;
                if ($objTestOfUser->obj_result) {
                    $resultUser = json_decode(trim($objTestOfUser->obj_result));
                }

                //Tìm tất cả các câu hỏi của TestId này
                $uid = getCurrentUserId();
                $mQuestInTest = \App\Models\QuizTestQuestion::where(['test_id' => $testId])->get();
                if (!$mQuestInTest) {
                    bl("Not found any question?");
                    goto _END;
                }
                $mQuestInTest = $mQuestInTest->toArray();

                $cQid = request('qid');

                ?>
                <div class="topban pos3">
                    <i class="fa fa-check-square" style="font-size: 80%"> </i> Danh sách câu hỏi
                    <p></p>
                <?php


                //                echo "<br/>\n";
                $cc = 1; $cauSo = 1;
                $correct = 0;
                $daLam = 0;
                $nextId = 0;
                $lastId = 0;
                $tongSoCau = count($mQuestInTest);
                //Liệt kê ra all câu hỏi:
                foreach ($mQuestInTest AS $objTestQuest) {
                    $objTestQuest = (object)$objTestQuest;
//                    echo "<br/>\n $objSession->question_id";
                    if ($q = \App\Models\QuizQuestion::find($objTestQuest->question_id)) {
//                        echo "<br/>\n $q->name ";
                        $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('qid', $q->id);
                        $fid = $objTestQuest->question_id;

                        $padClass = '';
                        //Nếu câu hỏi này đã được trả lời

                        $correctText = '-';
                        if ($userPostRet =  $resultUser->$fid ?? ''){
                            $daLam++;
                            $padClass = "done";

                            if(\App\Models\QuizTool::checkQuizTrue($q, $userPostRet))
                                $correct++;

                            //Nếu hết time làm thì  mới hiển thị đúng sai:
                            if(\App\Models\QuizTool::hetTimeLamBai($objSession))
                            if(\App\Models\QuizTool::checkQuizTrue($q, $userPostRet))
                                $correctText = '<i class="fa fa-check-circle" style="color: blue"> </i>';
                            else
                                $correctText = '<i class="fa fa-times-circle" style="background-color: red; color: white; border: 1px solid red; border-radius: 50%"></i>';
                        }

                        if(!$nextId && $lastId)
                            $nextId = $q->id;
                        if ($cQid == $q->id || (!$cQid && $cc == 1)) {
                            if (!$cQid && $cc == 1){
                                $cQid = $q->id;
                            }


                            if(!$lastId)
                                $lastId = $cQid;

                            $cauSo = $cc;
                            echo " <a class='qid $padClass doing' href='$link' style=''>$cc <br> $correctText </a>  ";
                        } else {
                            echo " <a class='qid $padClass' href='$link'>$cc <br> $correctText </a>  ";
                        }
                        $cc++;
                    }
                }



                $diem = number_format( 100 * $correct / $tongSoCau,1);
                if(\App\Models\QuizTool::hetTimeLamBai($objSession)){
                    echo "<br/>\n <button class='btn btn-sm btn-primary' > Điểm: $diem/100 </button> Số câu đúng: $correct/$tongSoCau câu , Số câu đã làm: $daLam";
                }
                ?>
                </div>

                <div class="topban mt-3 pos4">
                    <?php

                if($cQid){

                if (!$objCQuest = \App\Models\QuizQuestion::find($cQid)) {
                    bl("Not found QID:" . htmlspecialchars($cQid));
                    goto _END;
                }

                $obj = new qA_W3School();
                $obj->codeCss();
                //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //        print_r($obj);
                //        echo "</pre>";

                $translatedText = \App\Models\QuizTool::translateW3SchoolToVnIgnoreTextInQuote($objCQuest->content);
                $linkNext = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('qid', $nextId);
                if($nextId)
                    echo "\n <button class='btn btn-info btn-sm' style='float: right; color: white'> <a style='color: white' href='$linkNext' title='$nextId'> Câu tiếp theo <i class='fa fa-caret-right'></i> </a> </button>";

//                if(!$objCQuest->content_vi)

                if(1)
                {
                    $text = ($objCQuest->content);
                    $translatedText = \App\Models\QuizTool::translateW3SchoolToVnIgnoreTextInQuote($text);
//                    echo $tr->getLastDetectedSource(); // Output: en
                    if($translatedText){
                        $objCQuest->content_vi = $translatedText;
                        $objCQuest->update();
                    }
                }

                $cont = $objCQuest->content;
                if($objCQuest->content_vi)
                    $cont = $objCQuest->content_vi;

                if(isSupperAdmin_())
                {

                    function replaceContentShow($cont){
                        $mm = [
                            '<code class="w3-codespan">',
                            '</code>',
                            '<br>',
                            '<p>',
                            '</p>'

                        ];

                    }

                }
                {
                    $cont = str_replace('<code class="w3-codespan">', "___PLACEHOLDER1___" , $cont);
                    $cont = str_replace('</code>', "___PLACEHOLDER2___" , $cont);
                    $cont = str_replace('<br>', "___PLACEHOLDER3___" , $cont);

                    $cont = htmlspecialchars($cont);
                    $cont = str_replace( "___PLACEHOLDER1___", '<code class="w3-codespan">' , $cont);
                    $cont = str_replace( "___PLACEHOLDER2___" , '</code>', $cont);
                    $cont = str_replace( "___PLACEHOLDER3___" , '<br>',$cont);
                }

                echo " <i> <b> Câu số $cauSo </b></i> : <span class='unselectable' data-code-pos='ppp17042530061961'>    $cont  </span> ";


                $contentQ = str_replace("<br>", '', $objCQuest->content_textarea);
                $contentQ = str_replace("<br>", '', $contentQ);
                $contentQ = str_replace("\r\n\r\n", "\r\n", $contentQ);
                ?>

                    <p></p>
                <div id="assignmenttext" style=''></div>
                <pre id="assignmentcontainer" style="overflow:auto"></pre>
                <div id="assignmentcode" style="display: none"><?php echo $contentQ ?></div>
                {{--                        <button id="confirm_result"> <b> Confirm </b> </button>--}}

                <button id="post_ret" class="btn-sm btn-info"> Ghi lại</button>
                        &nbsp;
                <i style="font-size: small">Hướng dẫn: Điền vào các ô Trắng phía trên, và Bấm Ghi lại khi xong từng câu </i>
                <?php

                if(!\App\Models\QuizTool::hetTimeMoCauTraLoi($objSession)){
                    echo "<div style='background-color: lavender; margin: 20px 0px 10px 0px; padding: 10px; border: 1px  solid #ccc'>";
                    echo " <p> <b> Đáp án, kết quả từng ô: </b> </p><ol style='margin-bottom: 0px'>";
                    $cc = 0;
                    if($objCQuest->answer){
                        $mAns = explode("\n", $objCQuest->answer);
                        foreach ($mAns  AS $one){

                            $cc++;
                            echo "<li> Ô thứ $cc: <span style='color: red'> " . htmlentities($one) . "</span> </li>";
                        }
                    }
                    echo "</ol>";
                    echo "</div>";
                }
                ?>
                        </div>
                        <?php

                if (isSupperAdmin_()) {

                    echo "<div class='adm_zone' style='color: transparent'> ADMINx:
<a target='_blank' style='background-color: transparent; color: transparent' href='/admin/quiz-question/edit/$cQid'> QID $cQid</a>  <pre style='color: transparent'> " . htmlentities($objCQuest->answer) . " </pre>  ";
                    echo "<br/>\n <a target='_blank' href='$objCQuest->refer'> Link Org </a> </div>";

                }



                $obj->codeJs();

                $answQid = $resultUser->$cQid ?? '';
                $mAnsOfQid = explode("\n", $answQid);
                }
                ?>
                <?php
                _END:
                ?>
            </div>
        </section>
        <!-- /.content -->

        <?php

        _END1:

        ?>
    </div>

    <script>

        window.onload = function () {

            //Lấy từ DB ra để điền lại KQ vào input box


            {{--let retUser = JSON.parse('<?php echo json_encode(explode("\n", json_decode($resultUser)?->$cQid ?? '')) ?>');--}}
            let retUser = <?php echo json_encode($mAnsOfQid ?? '') ?>;
            console.log("retUser = ", retUser);
            let cc = 0;
            $("#assignmentcontainer").find('input.editablesection').each(function (elm) {
                console.log(" VAL ", this.value);
                if (retUser[cc]) {
                    this.value = retUser[cc];
                }
                cc++;
            })


            $("#post_ret").on('click', function () {
                console.log("... OK 123");

                let user_token = jctool.getCookie('_tglx863516839');

                let mAns = []
                $("#assignmentcontainer").find('input.editablesection').each(function (elm) {

                    console.log(" VAL ", this.value);
                    this.value = this.value.trim();
                    if (this.value.length)
                        mAns.push(this.value);

                })

                if (mAns.length < 1) {
                    alert("Bạn chưa nhập câu trả lời?");
                    return;
                }

                let url = "/api/quiz-test/doTestPostResult";
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {ans: mAns, qid: '<?php echo $cQid ?? '' ?>', testIdOfUser: '<?php echo $testIdOfUser ?>'},
                    async: false,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    success: function (data, status) {
                        hideWaittingIcon();
                        console.log("Data ret: ", data, " \nStatus: ", status);

                        if (data.payload) {
                            showToastInfoTop(data.payload);
                        } else {
                            alert("Có lỗi: " + JSON.stringify(data))
                        }
                    },
                    error: function (data) {
                        hideWaittingIcon();
                        console.log(" DATAx ", data);
                        if (data.responseJSON && data.responseJSON.message)
                            alert('Error call api: ' + "\n" + data.responseJSON.message)
                        else
                            alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                    }
                });

            })

        }

    </script>

    <?php
    if(!isSupperAdmin_()){
    ?>
    <script>
        document.addEventListener("contextmenu", (event) => {
            event.preventDefault();
        });
    </script>
    <?php
    }
    ?>

@endsection

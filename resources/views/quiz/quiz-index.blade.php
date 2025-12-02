@extends(getLayoutNameMultiReturnDefaultIfNull())

<?php
$qid  = intval(request('qid'));
?>

@section("title")
    1000 Bài Toán Tư duy nâng cao toàn diện - Tiểu học - Bài số  {{$qid}}
@endsection

@section("css")
    <style>
        .one_question {
            min-height: 300px;
            margin: 15px 0px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #eee;
        }

        .question_title {
            font-size: 90%;
            font-style: italic;
            display: block;
            padding: 3px 1px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }


    </style>

@endsection

@section("content")
    <style>
        .quiz_cont img {
            /*width: 100%;*/
            max-width: 600px;
        }
    </style>
    <div class="container quiz_cont" data-code-pos='ppp17222688278651' style="min-height: 600px">

        <div class="p-3 mt-3" style="text-align: center">
            <h1>
                1000 Bài Toán Tư duy nâng cao - Toàn diện - Tiểu học
            </h1>
        </div>

        <p>
            <i>
                - Giới thiệu: Các bài toán ở đây tổng hợp theo các tài liệu Toán Singapore, Toán Kangaru (Úc), Violympic ...
                toàn diện 99% các bài tập tư duy tiểu học.
                Tất cả là các bài toán tư duy, nên các bài toán kỹ năng cơ bản thông thường kiểu cộng trừ nhân chia... sẽ không có ở đây.
                <br> Hiệu quả các bài tập đã được kiểm định qua các Kỳ thi Quốc tế như Timo, Seamo, Kangaroo, Violympic...
                <br>
                - Các bài toán đã được tập hợp từ Lớp 1 đến Lớp 4 (Ngày cập nhật: 01.12.2024)
                <br>
                - Hướng dẫn: các em sẽ làm từ bài đầu tiên, không nên bỏ bài, bài nào làm chưa tốt đánh dấu lại (Mã số câu hỏi) vào 1 quyển vở riêng để sau làm lại.
                Nếu làm lại vẫn chưa tốt, đánh dấu thêm một lần... đến khi làm tốt hiểu rõ. Phần này nên có bố mẹ hoặc thầy cô theo dõi.Nếu cần hỗ trợ xin liên hệ với chúng tôi theo Nick Zalo bên dưới
            </i>
        </p>
        <?php

        $limit = 1;

        $pgnstr = '';
        $url = \LadLib\Common\UrlHelper1::getUriWithoutParam();
//        $tt = \App\Models\QuizQuestion::whereIn("parent_id", [2,157,158])->where('cat1',1)->count();

        $mmQ = \App\Models\QuizQuestion::select("id")->whereIn("parent_id", [2,157,158])->where('cat1',1)->get();
        $mmQ = $mmQ->pluck('id')->toArray();

        if(!$qid)
            $qid = $mmQ[0];

        $tt = count($mmQ);

        $link = \LadLib\Common\UrlHelper1::getUriWithoutParam();
//        echo "\n LINK = $link";
        ?>


        <?php
        $currentIndex = array_search($qid, $mmQ);
        $prevQid = $currentIndex > 0 ? $mmQ[$currentIndex - 1] : null;
        $nextQid = $currentIndex < count($mmQ) - 1 ? $mmQ[$currentIndex + 1] : null;
        ?>



        <div class="form-group-sm" style="width: 400px; display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ $link . '?qid=' . $mmQ[0] }}" class="btn btn-sm btn-outline-primary mx-2">First</a>
            @if($prevQid)
                <a href="{{ $link . '?qid=' . $prevQid }}" class="btn btn-sm btn-outline-primary">Prev</a>
            @else
                <a href="#" class="btn btn-sm btn-outline-default">Prev</a>
            @endif
            <select class="form-control form-control-sm mx-2"
                    style="font-size: 80%"
                    onchange="location.href='<?php echo "$link?qid="; ?>' + this.value">
                <?php
                $cc = 0;

                foreach ($mmQ AS $k => $v) {
                    $cc++;
                    if($qid == $v)
                        echo "\n <option value='$v' selected> $cc. Mã số: $v </option>";
                    else
                        echo "\n <option value='$v'> $cc. Mã số: $v </option>";
                }
                ?>
            </select>

            @if($nextQid)
                <a href="{{ $link . '?qid=' . $nextQid }}" class="btn btn-sm  btn-outline-primary">Next</a>
            @else
                <a href="#" class="btn btn-sm btn-outline-default">Next</a>
            @endif
            <a href="{{ $link . '?qid=' . end($mmQ) }}" class="btn btn-sm btn-outline-primary mx-2">Last</a>
        </div>


        <?php


        $obj = \App\Models\QuizQuestion::find($qid);
        echo "\n\n<div data-pos='324523453453' class='question_list' style=''>\n";


            echo "\n<div data-pos='324534534534' class='one_question' style=''>\n";
            $padE = '';
            if(isSupperAdmin_())
            {
                $padE = " <a target='_blank' href='/admin/quiz-question/edit/$obj->id'> [e] </a>";
            }
            echo " <div style='' class='question_title'> Mã số câu hỏi : $obj->id  $padE </div>\n";
            echo "\n <div style='' class='question_content'>  $obj->content </div> \n";
//            echo "<br/>\n <span style='font-size: smaller; font-style: italic; color: gray'>Nguồn: $obj->name </span> ";
            echo "\n</div>\n";

        echo "\n</div>\n\n";

//        echo $pgnstr;
        ?>

        <br><br>
    </div>
@endsection

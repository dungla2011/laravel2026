@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    Toán Tư duy nâng cao toàn diện - Tiểu học
@endsection

@section("css")
    <style>
        .one_question {
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
    <div class="container quiz_cont" data-code-pos='ppp17222688278651'>

        <div class="p-3 mt-3" style="text-align: center">
            <h1>
            Toán Tư duy nâng cao toàn diện - Tiểu học
            </h1>
        </div>

        <p>
            <i>
            Hướng dẫn: các em sẽ làm từ bài đầu tiên, không nên bỏ bài, bài nào làm chưa tốt đánh dấu lại (Mã số câu hỏi) vào 1 quyển vở riêng để sau làm lại.
            Nếu làm lại vẫn chưa tốt, đánh dấu thêm một lần... đến khi làm tốt hiểu rõ. Phần này cần bố mẹ hoặc thầy cô theo dõi.
            </i>
        </p>
        <?php

        $pgnstr = '';
        $url = \LadLib\Common\UrlHelper1::getUriWithoutParam();
        $tt = \App\Models\QuizQuestion::whereIn("parent_id", [2,157,158])->where('cat1',1)->count();
        $cpage = request('page');
        if (!$cpage)
            $cpage = 1;
        $mp = \LadLib\Common\clsPaginator2::getArrayLinkPaginator($url, $tt, 10, $cpage);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mp);
//        echo "</pre>";
        foreach ($mp AS $p => $link) {
            if ($link == '#') {
                $pgnstr .= " ... ";
                continue;
            }
            if ($p == 'current') {
                $pgnstr .= "$cpage ";
                continue;
            }
            $p = ucfirst($p);
            $pgnstr .= " <a style='display: inline-block; margin: 2px; padding: 3px 6px; border: 1px solid #ccc' href='$link'> $p </a> ";
        }

        $pgnstr .=" ($tt bài) ";

        echo $pgnstr;

        $ofs = ($cpage - 1) * 10;

        $mm = \App\Models\QuizQuestion::whereIn("parent_id", [2,157,158])->where('cat1',1)->limit(10)->offset($ofs)->get();

        foreach ($mm AS $obj) {
            echo "<div data-pos='3245234534534534' class='one_question' style=''>\n";


            echo " <span style='' class='question_title'> Mã số câu hỏi : $obj->id ";

            if(isSupperAdmin_())
            {
               echo " <a target='_blank' href='/admin/quiz-question/edit/$obj->id'> [e] </a>  </span>";
            }
            echo "\n $obj->content";
//            echo "<br/>\n <span style='font-size: smaller; font-style: italic; color: gray'>Nguồn: $obj->name </span> ";
            echo "</div>";
        }

        echo $pgnstr;
        ?>

        <br><br>
    </div>
@endsection

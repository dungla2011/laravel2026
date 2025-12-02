<?php

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r(headers_list());
//    echo "</pre>";
//    foreach (headers_list() as $header)
//        header_remove($header);

//$uid = getCurrentUserId();
//if(!$uid)
//{
//    http_response_code(401);
//    echo "<br/>\n";
//    bl("Bạn hãy <a href='/login'> Đăng nhập </a>");
//    return;
//    die();
//}


?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')Kết quả - <?php
    echo \App\Models\SiteMng::getTitle()
    ?>
@endsection

@section('description')<?php
    echo \App\Models\SiteMng::getDesc();
    ?>@endsection

@section("css")

@endsection

@section('content')

    <div class="container mt-4">

        <div class="row">

            <div class="col-sm-12">
                <?php
                $uid = getCurrentUserId();
                if(!$uid)
                    echo " <h1> Bạn chưa đăng nhập! </h1>";
                else
                    echo \App\Models\TypingTestResult_Meta::getKetQuaHtml($uid, 10);

                ?>
            </div>
        </div>

    </div>

    </div> <!-- wrap -->


@endsection


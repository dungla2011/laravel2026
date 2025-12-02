<?php
    $dm = \LadLib\Common\UrlHelper1::getDomainHostName();

    if($dm !== 'tailieuchuan.net'){
        echo "<br/>\n";
        echo "<br/>\n";
        bl('<a href="https://tailieuchuan.net/game/duoi-hinh-bat-chu"> Game chuyển sang Tại đây </a>');
        return;
    }

?>

@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    Đuổi hình bắt chữ - 2710 câu mới nhất <?php echo date("Y") ?>
@endsection

@section("css")
    <style>

    </style>
@endsection

@section('content')
    <?php
        if(!isSupperAdminDevCookie()){
            //die('Under construction! <a href="/login"> LOGIN </a>');
        }
    ?>
    <style>

        .title_sent {
            margin-top: 30px;
        }

        #show_hint2{

            margin: 10px;
        }


        .button_zone button {
            margin-right: 10px;
            box-shadow: 0 0 5px 5px #ddd;
        }

        span.one_char_hint {
            color: royalblue;
            text-align: center;
            margin: 1px;
            font-size: 140%;
            display: inline-block;
            /*border: 1px solid green;*/
            padding: 1px;
            font-family: Tahoma !important;

        }

        .select_sent {
            padding: 5px; color: royalblue; border-color: royalblue; border-radius: 5px;
            margin-top: 20px;

        }

        .pagination ul {
            display: flex;
            list-style-type: none;
            padding: 0;
        }

        .pagination ul li {
            margin: 0 5px;
        }

        a.active {
            color: red;
            font-weight: bold;
            border: 1px solid red;
            padding-right: 3px;
            padding-left: 3px;
        }

        .one_item {
            display: inline-block;
            background-color: royalblue;
            border-radius: 50%;
            font-size: 80%;
            color: white;
            padding: 3px;
            margin-top: 5px;
            width: 30px;
            text-align: center;
            border: 1px solid #ccc;
        }

        .img_bg {
            text-align: center; width: 400px; height: 320px;
            margin: 40px auto 20px auto!important ;
            /*background-size: cover;*/
            /*background-position: center;*/
            position: relative;
        }

        @media only screen and (max-width: 900px) {
            .title_sent {
                margin-top: 5px;
            }
            .select_sent {
                margin-top: 5px;
            }
            h1 {
                font-size: 20px;
            }
            h3 {
                font-size: 18px;
            }
            .img_bg {
                width: 100%;
                margin-top: 20px!important;
            }
            .caption1 .txt1 {
                font-size: 20px;
            }

            .caption1 a {
                font-size: 15px !important;
            }

            .jumbotron{
                padding: 20px;
            }
        }

    </style>


    <?php
        $limit = 50;
        $page = request('page',1);
    $sent = request('sent',1);
    ?>

    <div class="jumbotron container pt-3 pb-3" style="color: royalblue">


        <?php


            $totalItems = \App\Models\FileUpload::where("name", "LIKE", "Đuổi hình bắt chữ%")->count();
//            echo "Total : $totalItems";
            $items = \App\Models\FileUpload::where("name", "LIKE", "Đuổi hình bắt chữ%")
                ->paginate($limit, ['*'], 'page', $page);
                // Now you can loop through the items

            $nItem = count($items);

if(count($items) == 0){
    bl("Not found any item!");
    return;
}

            $itemA = $items->toArray();

            $cc = 0;


            $linkNext = $linkPre = '#';
            if($sent >= 1 && $sent < $nItem){
                $linkNext = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sent', $sent + 1);
                $linkPre = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sent', $sent - 1);
            }
            else{
                $linkNext = \LadLib\Common\UrlHelper1::setUrlParamArray(null, ['page' => $page + 1, 'sent' => 1]);
            }

            if($sent <= 1){
                $linkPre = '#';
            }

//            if($sent >= $limit)
//                $linkNext = '#';
//
//            echo "<br/>\n $linkNext";
//            echo "<br/>\n $linkPre";


            $cItem = $items[$sent - 1];
            $img = $cItem->getCloudLink();
//
//            dump($cItem);
//            return;


            ?>
        <div data-code-pos='ppp172182530511' style="text-align: center; color: royalblue" class="mt-3">

            <h1 class="title_sent" style="color: royalblue;
            text-shadow: 1px 2px 5px #ccc;
            "> Đuổi hình bắt chữ - Phần <?php echo $page ?> </h1>

            <select style='' class="select_sent" onchange='if (this.value) window.location.href=this.value'>
                <?php


                foreach ($items as $item) {
                    $cc++;
                    if($item instanceof \App\Models\FileUpload);


                    // Your code here
                    $link2 = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sent', $cc);

                    $style = null;

                    if($sent == $cc){

                        $style = "selected";
                    }

                    echo "<option $style value='$link2'> Câu $cc / $nItem</option>";
                }


                ?>
            </select>
        </div>


        <?php

        $lkAdm = "#";
        if(isSupperAdmin_()) {
            $lkAdm = "/admin/file/edit/" . $cItem->id;
        }
        ?>


{{--        background-image: url("<?php echo $img ?>");--}}
            <div data-code-pos="ppp1721779908766" style=''

                 class='m-4 img_bg' >

                <a href="<?php echo $lkAdm ?>">
                <img src="<?php echo $img ?>" alt="" style="width: 100%; height: 100%;
                box-shadow: 0 0 5px 5px #ddd;
                ">
                </a>


                <div id="show_hint" data-kq="<?php echo $cItem->comment ?>" class="hide_ans" style="position: absolute;
                bottom: 0px; height: 55px; width: 100%; background-color: #ddd;
                border: 1px solid #ddd;
                color: black; font-size: 20px; font-weight: bold; text-align: center;
                display: flex; justify-content: center; align-items: center;
                color: gray
                ">
                    ???
                </div>

                <div id="show_hint2"></div>
            </div>

            <div class="button_zone" data-code-pos='ppp17217799041351' style='text-align: center;
            margin: 10px auto!important;
            '>

                <a href="<?php echo $linkPre ?>">
                <button class="btn btn-sm btn-info"> Câu trước</button>
                </a>

                <button onclick="goiY()" class="btn btn-sm btn-outline-danger">Gợi ý</button>

                <button class="btn btn-sm btn-primary" id="showAns">Kết quả</button>

                <a href="<?php echo $linkNext ?>" id="nextSent">
                <button class="btn btn-sm btn-warning"> Câu sau</button>
                </a>

                <div class="mt-3" style="font-size: 80%; color: gray">
                    Phím Tắt trên máy tính: Bấm nút Mũi tên sang phải để Gợi ý và sang Câu Tiếp theo

                </div>

            </div>

            <?php

            $totalPages = ceil($totalItems / $limit);
        ?>

    </div>


    <hr>
    <div data-code-pos='ppp17217799184181' class="jumbotron container mt-2 p-3">
    <div class="m-3" style="text-align: center; margin: 0 auto">
        <div style="margin-bottom: 10px">
            <b>
        Chọn Phần (mỗi phần 50 câu)
            </b>
        </div>

            <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($page == $i) ? 'active' : '' ?>" style="margin: 3px" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>

    </div>
    </div>
    <script>

        document.addEventListener('DOMContentLoaded', function(event) {

            //Nếu bấm phím mũi ten xuong gọi hàm goiY()
            document.addEventListener('keydown', function(event) {
                //Nếu bấm phím mũi ten xuong gọi hàm goiY()
                if (event.key.toLowerCase() === 'arrowdown') {
                    goiY();
                }
                if (event.key.toLowerCase() === 'arrowright') {
                    goiY();
                }

            });

        });


        let nClickShowHint = 0
        function goiY(){
            var kq = document.getElementById('show_hint').getAttribute('data-kq');
            kq = kq.replace(/\s+/g, ' ');
            // alert(kq);

            let lenx = kq.length;

            let str = "";
            let nChar = 0;
            let nWord = kq.split(" ").length;
            for(let i = 0; i < lenx; i++){

                if(kq[i] === ' ') {
                    str += `<span class="one_char_hint" > - </span>`;
                    if(nClickShowHint == i){
                        nClickShowHint++;
                    }
                }
                else{
                    nChar++;
                    if(i < nClickShowHint )
                        str += `<span class="one_char_hint" > ${kq[i]} </span>`;
                    else
                        str += `<span class="one_char_hint" > _ </span>`;
                }
            }

            nClickShowHint++;

            console.log("nClickShowHint: " + nClickShowHint + " / " + lenx);

            if(nClickShowHint > lenx + 1){
                //Bấm vào nút Câu sau
                document.getElementById('nextSent').click();
            }

            document.getElementById('show_hint').innerHTML = str;

            // document.getElementById('show_hint2').innerHTML = "Số từ: " + nWord + " - Số ký tự: " + nChar;
            // document.getElementById('show_hint2').style.display = 'block';

        }

        document.addEventListener('keydown', function(event) {
            if (event.key.toLowerCase() === 'x') {
                goiY();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key.toLowerCase() === 'v') {
                document.getElementById('nextSent').click();
            }
        });

        document.querySelectorAll('.hide_ans').forEach(function(element) {
            element.addEventListener('click', function() {
                this.style.display = 'none';
            });
        });

        document.getElementById('showAns').addEventListener('click', function() {
            document.querySelectorAll('.hide_ans').forEach(function(element) {

                    if (element.style.display === 'none') {
                        element.style.display = 'flex';
                    } else {
                        element.style.display = 'none';
                    }

            });
        });


    </script>

@endsection

@section('js')
    <script>



    </script>
@endsection

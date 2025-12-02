@extends(getLayoutNameMultiReturnDefaultIfNull())



@section("og_desc")<?php
echo \App\Models\SiteMng::getDesc()
?>@endsection

@section("og_image")<?php
echo \App\Models\SiteMng::getLogo()
?>@endsection

@section("title")<?php
echo \App\Models\SiteMng::getTitle()
?>@endsection


@section('content')

    <style>

        .carousel-caption {
            text-shadow: 2px 2px #222222;
        }

        .splide__slide img {
            width: 100%;
            height: auto;
        }
    </style>

    <?php
    use App\Models\BlockUi;

    $so_hieu_bang_tn = trim($_REQUEST['so_hieu_bang_tn'] ?? '');
    $so_hieu_bang_tn = strtoupper($so_hieu_bang_tn);

    $ngay_sinh = trim($_REQUEST['ngay_sinh'] ?? '');

    $ngay_sinh = str_replace([" "], '', $ngay_sinh);
    $ngay_sinh = str_replace(["/"], '-', $ngay_sinh);
    $so_hieu_bang_tn = str_replace([" "], '', $so_hieu_bang_tn);

    ?>




    <div class="container pt-2" data-code-pos='ppp16897794803421' style="min-height: 400px">


        <div class="container">
            <div class="row">
                <div class="col-md-3">
                </div>
                <div class="col-md-6 pt-5">

                    <div class="jumbotron" style="border: 1px solid #ccc; padding: 20px; background-color: snow">
                        <h5> Tra cứu thông tin văn bằng </h5>
                        <form class="custom-form mt-4" method="get">
                            Nhập Số hiệu bằng tốt nghiệp <input type="text" class="form-control" name="so_hieu_bang_tn" value="<?php echo $so_hieu_bang_tn ?? '' ?>">

                            <br>
                            Nhập Ngày sinh (Ví dụ: <b>30/01/2000</b> hoặc <b>30-01-2000</b>) <input type="text" class="form-control" name="ngay_sinh" value="<?php echo $ngay_sinh ?? '' ?>">
                            <button type="submit" class="btn btn-primary mt-3">Tra cứu</button>

                            <?php
                            if($_REQUEST)
                            if(!$so_hieu_bang_tn  || !$ngay_sinh)
                                bl("Bạn cần nhập đủ thông tin!");
                            ?>

                        </form>
                    </div>

                </div>

                <div class="col-md-3">
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8 pt-5">
                    <div class="jumbotron" style="border: 1px solid #ccc; padding: 20px; background-color: snow">
                        <h4 style='text-align: center' class='mb-3'>  Kết quả tra cứu: </h4>


{{--                        <br>--}}
                    <?php

                    if ($so_hieu_bang_tn && $ngay_sinh) {

                        $ngay_sinh = nowy(strtotime($ngay_sinh));
                        $meta = \App\Models\HatecoCertificate::getMetaObj();
                        //echo "<br/>\n xx $birthday ";
                        $obj= \App\Models\HatecoCertificate::where(
                            ["so_hieu_bang_tn" => $so_hieu_bang_tn,
                                'ngay_sinh'=>$ngay_sinh])->first();

                        if(!$obj){
                            bl("Không tìm thấy thông tin!");
                        }else
                        {

                            echo "\n<table class='table table-striped'>";
                            $obj1 = (object) $obj->toArray();

                            foreach ($obj1 AS $key=>$val){
                                echo "<tr>";
                                if(in_array($key, ['id', 'name', 'user_id', 'status', 'created_at', 'updated_at','deleted_at','image_list' ,'log','',]))
                                    continue;
                                $fname = $meta->getDescOfField($key);
                                echo "\n <td> $fname </td>";
                                echo "\n <td> $val </td>";
                                echo "</tr>";
                            }

                            echo "\n</table>";

                        }

                    }
                    else{
                    }
                    ?>
                    </div>
                </div>
                <div class="col-md-2">
                </div>

            </div>




@endsection

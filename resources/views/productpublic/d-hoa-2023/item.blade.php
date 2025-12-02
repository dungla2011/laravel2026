
<?php

$fid = request('id');
$obj = \App\Models\Product::find($fid);
if(!$obj){
    blrt("Not found object: $fid");
    return;
}

if ($obj instanceof \App\Models\Product) ;
$img = $obj->getThumbInImageListWithNoImg();

$admLink = $obj->getEditLinkAdm();

$brc = $obj->getBreakumPathHtml(50);

?>

@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("og_desc")<?php
    if($obj)
        echo $obj->getMetaDesc();
    ?>@endsection

@section("og_image")<?php
    if($obj)
        echo $obj->getThumbInImageListWithNoImg();
    ?>@endsection

@section("title")<?php
    if($obj)
        echo $obj->getName() . " | " .\App\Models\SiteMng::getTitle();
    else
        echo "Sản phẩm: " .\App\Models\SiteMng::getTitle();
    ?>@endsection

@section("css")

    <style>
        .brc_path {
            /*font-weight: bold;*/
            display: inline-block;
            /*margin-bottom: 15px;*/
        }

        .brc_path span {

            max-width: 500px;
            display: inline-block;
            /*text-overflow: ellipsis;*/
            /*white-space: nowrap;*/
            /*overflow: hidden;*/
        }
        .img-product{
            text-align: center;
            padding-top: 10px;

        }
        .img-product img{
            max-width: 400px;
        }

    </style>
@endsection

@section("content")

    <div class="container pt-3" data-code-pos="ppp1690297726756">

        <div class="brc2" style="">
            <i class="fas fa-table"></i>
            <a href="/san-pham"> Sản phẩm / </a>
            <?php
            echo $brc ? $brc : "Sản phẩm";
            ?>
        </div>

        <div class="row">
            <div class="col-sm-6 text-center img-product">

                {{--                /template/d-hoa/images/dieuhoa-pana.jpg--}}

                <?php

                ?>

                <img src="<?php echo $img ?>" class="" alt="">

            </div>
            <div class="col-sm-6">
                <h1 style="font-size: 150%" class="fw-bold product-title qqqq1111">
                    <?php

                    \App\Models\BlockUi::showEditLink_($admLink, "EDIT this item");

                    ?>
                        {{$obj->name}}
                </h1>

                <b>
                    <p class="product-line">
                        Giá: <span style="color: red; font-weight: bold;"> <?php
                        if(!$obj->price)
                            echo "Liên hệ";
                        else{
                            echo number_formatvn0($obj->price) . ' VND ';

                            if($obj->price1){
                                echo "\n &nbsp <span style='text-decoration: line-through'>";
                                echo number_formatvn0($obj->price1) . ' VND <span>';
                            }

                        }
                        ?>
                        </span>
                    </p>
                </b>

                <?php
                echo $obj->summary;
                ?>
                <p></p>


{{--                <br>--}}
{{--                <button class="btn btn-info text-white">Đặt mua</button>--}}
            </div>
        </div>

        <div class="product-detail mt-4">

            <p class="tab active tab_detail_1">Chi tiết</p>
{{--            <p class="tab tab_detail_2">Đánh giá</p>--}}
{{--            <p class="tab tab_detail_3">Hỏi đáp</p>--}}

            <div class="tabl_detail tab_detail_1">
                <?php

                echo $obj->content;

                ?>
            </div>
            <div class="tabl_detail tab_detail_2" style="display: none;">
                Chi tiết 2
            </div>
            <div class="tabl_detail tab_detail_3" style="display: none;">
                Chi tiết 3
            </div>

        </div>
    </div>
@endsection

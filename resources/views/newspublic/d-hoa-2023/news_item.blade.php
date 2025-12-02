<?php

$id = request("id");


$obj = \App\Models\News::find($id);
if(!$obj){
    blrt("Không tồn tại tin tức này!");
    return;
}

if(!$obj->status){
    blrt("Không xem được tin này!");
    return;
}
?>

@extends(getLayoutNameMultiReturnDefaultIfNull())


@section("title")<?php
        echo $obj->getName() . " : ".\App\Models\SiteMng::getTitle()
        ?>@endsection

@section("og_title")<?php
        echo $obj->getName() . " | ". \App\Models\SiteMng::getTitle()
        ?>@endsection

@section("og_desc")<?php
    if($obj)
        echo $obj->getMetaDesc();
    ?>@endsection

@section("og_image")<?php
    if($obj)
        echo $obj->getThumbInImageListWithNoImg();
    ?>@endsection

@section("css")
    <style>

        h1 {
            font-size: 2em;
        }
        .brc_path{
            display: inline-block;
        }
        .one_news img{
            width: 100%;
        }


    </style>
@endsection

@section("content")

<div class="container">
    <?php

    if($obj->parent_id){
        $fold = new \App\Models\NewsFolder();
        if($fold = $fold->find($obj->parent_id)){
            $metaFold = new \App\Models\NewsFolder_Meta();
            $link = $metaFold->getPublicLink($fold);
        }
    }



    if($obj instanceof \App\Models\News);


    $brc = $obj->getBreakumPathHtml(50);

    ?>
    <div class="row" data-code-pos='ppp16897382176151'>
        <div class="col-sm-9" style="" data-code-pos='ppp16897676653201'>
            <a href="/tin-tuc/"> Tin Tức / </a>
            <?php
            echo $brc
            ?>
            <div style="font-size: small; margin-top: 10px">
            <?php
            echo $obj->created_at
            ?>
            </div>
                <h1 class="qqqq1111">
                    <b>
                <?php
                    if(isSupperAdmin__()){
                        $meta = \App\Models\News::getMetaObj();

                        $link = $obj->getEditLinkAdm();
                        \App\Models\BlockUi::showEditLink_($link);

                    }
                echo $obj->getName();
                ?>
                </b></h1> <b>

                <?php
                echo $obj->summary
                ?>

            </b> <br>
            <br>
            <p>
            <?php
            echo $obj->content
            ?>
            </p>



            <div class="fb-comments" target="_top" data-order-by="reverse_time"
                 data-href="<?php
                 echo \LadLib\Common\UrlHelper1::getFullUrl()
                 ?>" data-width="auto" data-numposts="10" data-colorscheme="">
            </div>



            <hr>
            <h3>Các tin cũ hơn: </h3>


        </div>

        <div class="col-sm-3 qqqq1111" data-code-pos='ppp16897676499681'>

            <?php

            $ui = \App\Models\BlockUi::showEditButtonStatic('right-ads-news');

            ?>

            <div>
                <br>

                <?php
                if($ui)
                if($mImg = $ui->getAllImageList()){

                $mLink = explode("\n",$ui->getExtra());

                $cc = 0;
                foreach ($mImg AS $img){
                $cc++;
                ?>

                <a href="<?php if($mLink && isset($mLink[$cc])) echo $mLink[$cc] ?>">
                    <img style="width: 100%" src="{{$img}}">
                </a>
                <br><br>
                <?php
                }
                }
                ?>


            </div>
        </div>
    </div>





    <?php


    _END:
    ?>

</div>


@endsection

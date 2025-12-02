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


@section("title")
        <?php
        echo $obj->name . " : ".\LadLib\Common\UrlHelper1::getDomainHostName()
        ?>
@endsection

@section("og_title")<?php

        echo $obj->name . " - ". strtoupper(\LadLib\Common\UrlHelper1::getDomainHostName())
        ?>
@endsection

@section("css")
    <style>
        .ladcont img{
            width: 100%!important;
            height: auto!important;
        }
        .banner--clone {
            display: none;
        }
        .position-absolute {
            position: relative!important;
        }
        .navbar{
            padding-top: 0px!important;
            background-color: #393939;
        }
        .ladcont{
            padding-top: 20px;
        }

        body {
            font-weight: unset!important;
        }
        .one_news img{
            width: 100%;
        }

    </style>
@endsection

@section("content")
    <style>

    </style>
    <div class="ladcont">
        <div class="container">
            <?php


            $padBrc = '';
            if($obj->parent_id){
                $fold = new \App\Models\NewsFolder();
                if($fold = $fold->find($obj->parent_id)){
                    $metaFold = new \App\Models\NewsFolder_Meta();
                    $link = $metaFold->getPublicLink($fold);
                    $padBrc = "<a href='$link'> $fold->name </a> / ";
                }
            }

            ?>
            <div class="row one_news" data-code-pos='ppp16897382176151'>
                <div class="col-sm-9 top-brc" style="">
                    <div style="" class="brc">
                        <b>
                            <a href="/">Trang chủ </a> / <a href="/tin-tuc">Tin tức </a> / <?php echo $padBrc ?>     </b>
                    </div>
                    <div style="font-size: small; margin-top: 10px">
                    <?php
                    echo $obj->created_at
                    ?>
                    </div><h1 style="margin-top: 10px; font-size: 24px" data-code-pos='ppp17339733667521'>  <b>
                        <?php
                            if(isAdminACP_()){
                                $meta = \App\Models\News::getMetaObj();
                                $url = $meta->getAdminUrlWeb()."/edit/$obj->id";
                                echo "<a href='$url'>[E]</a>";
                            }
                        echo $obj->name;
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
                    <div style="min-height: 200px">
                       <?php

                           //List 5 tin cũ hơn
                            $list = \App\Models\News::where("id", "<", $obj->id)->where("status", 1)->orderBy("id", "desc")->limit(5)->get();
                            foreach($list as $item){
                                if($item instanceof \App\Models\News)
                                $link = $item->getLinkPublic();
                                echo "<a href='$link'>$item->name</a><br>";
                            }


                       ?>

                    </div>


                </div>

                <div class="col-sm-3" style="">

                    <div style="display: ">
                    <br>
                    <a id="editable_glx_news_52" href="#">
                        <img style="width: 100%" src="/images/store/glx_banner.jpg">
                    </a>

                    <br><br>
                    <a id="editable_glx_news_54" href="#">
                        <img style="width: 100%" src="/images/store/ads_quangcao_nghigiau.gif"></a>

                    <br><br>

                    <a id="editable_glx_news_53" href="#">
                        <img style="width: 100%" src="/images/store/suport_girl.png"></a>

                    <br><br>
                    </div>
                </div>
            </div>





            <?php
            _END:
            ?>

        </div>

    </div>
@endsection

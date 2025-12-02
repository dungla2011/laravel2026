@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")<?php

    $metaTitle = \App\Models\SiteMng::getTitle();

$title = 'Tin tức';


$strSearch = $isSearch = null;
if(request('stype') == 'service'){
    $isSearch = true;
    $strSearch = htmlspecialchars(strip_tags(request("search")));
}

$pid = $fold = null;
if($strSearch){
    $title .= " - Tim kiem $strSearch";
}
else{
    if ($pid = request('id')) {
        if ($fold = \App\Models\NewsFolder::find($pid)) {
            $title .= ": " . $fold->name;
        }
    }
}
echo $title . " | " . $metaTitle
?>@endsection

@section("og_title")<?php
echo "MyTree: Tin mới nhất"
?>@endsection

@section("og_desc")<?php
if ($fold)
    echo $fold->getMetaDesc();
?>@endsection

@section("css")
    <style>
        .new-cont img.new-img {
            width: 100%;
        }

        .new-cont {
            background-color: #eee;
            margin-bottom: 10px;
            border-radius: 5px;
            padding: 10px 0px
        }

        .new-cont .new-sum {
            color: #222222;
            font-size: 0.98em;
        }

        .new-cont .new-name {
            font-weight: bold;
            margin-top: 5px;
        }

        .brc {
            margin-bottom: 20px;
        }

        .brc_path {
            display: inline-block;
            overflow: hidden;

        }

        .brc_path span {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        @media screen and (max-width: 600px) {
            .new-cont .new-name {
                margin-top: 10px;
            }
        }

    </style>
@endsection

@section("content")
    <div class="container" style="min-height: 400px">

        <div class="row">

            <div class="col-sm-9" data-code-pos='ppp16897676530211'>

                <div class="brc qqqq1111">
                    <?php
                    \App\Models\BlockUi::showEditLink_("/admin/news");

                    echo "<a href='/tin-tuc'> Tin Tức </a> / ";
                    if ($fold && $fold instanceof \App\Models\NewsFolder) {
                        echo $fold->getBreakumPathHtml();
                    }
                    ?>
                </div>

                <?php
                $page = request('page');
                if(!$page)
                    $page = 1;
                $limit = 12;
                $offset = ($page - 1) * $limit;

                //$tt = \App\Models\News::where('status' ,1)->count();

                $fid = $pid;

                if ($fid){
                    $tt = \App\Models\News::where("parent_id", $fid)->where('status' ,1);
                    $mm = \App\Models\News::where("parent_id", $fid)->where("status", 1)->orderByDesc('created_at')->offset($offset)->limit($limit);
                    if($strSearch){
                        $tt = $tt->where('name', 'LIKE', "%$strSearch%");
                        $mm = $mm->where('name', 'LIKE', "%$strSearch%");
                    }
                }
                else{
                    $tt = \App\Models\News::where('status' ,1);
                    $mm = \App\Models\News::where("status", 1)->offset($offset)->orderByDesc('created_at')->limit($limit);
                    if($strSearch){
                        $tt = $tt->where('name', 'LIKE', "%$strSearch%");
                        $mm = $mm->where('name', 'LIKE', "%$strSearch%");
                    }
                }
                $tt = $tt->count();
                $mm = $mm->get();

//                if ($pid = request('id')){
//                    $mm = \App\Models\News::where(['status' => 1, 'parent_id' => $pid])->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
//                }
//                else{
//                    $mm = \App\Models\News::where(['status' => 1])->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
//                }


                if($mm){
                foreach ($mm AS $obj){
                if ($obj instanceof \App\Models\News) ;
                $link = $obj->getLinkPublic();
                ?>
                <div class="row new-cont">
                    <div class="col-sm-3">
                        <a href="<?php echo $link ?>">
                            <img class="new-img" src="<?php
                            echo $obj->getThumbInImageListWithNoImg()
                            ?>" alt="">
                        </a>
                    </div>
                    <div class="col-sm-9">
                        <a href="<?php echo $link ?>">
                            <div class="new-name">
                                <?php
                                echo $obj->name;
                                ?>
                            </div>
                            <div class="new-sum">
                                <?php
                                echo "$obj->summary";
                                ?>
                            </div>
                        </a>
                    </div>

                </div>
                <?php
                }
                }

                echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyle(null, $tt, $limit, $page);
                ?>

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

                    $mLink = explode("\n", $ui->getExtra());

                    $cc = 0;
                    foreach ($mImg AS $img){
                    $cc++;
                    ?>

                    <a href="<?php if ($mLink && isset($mLink[$cc])) echo $mLink[$cc] ?>">
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
    </div>
@endsection

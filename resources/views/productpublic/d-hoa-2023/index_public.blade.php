
<?php

$fold = new \App\Models\ProductFolder();
$fid = request('id');
$fold = $fold->find($fid);
if($fold instanceof \App\Models\ProductFolder);
$brc = '';
if ($fold)
    $brc = $fold->getBreakumPathHtml();

$strSearch = $isSearch = null;
if(request('stype') == 'product'){
    $isSearch = true;
    $strSearch = htmlspecialchars(strip_tags(request("search")));
}

?>


@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("og_desc")<?php
    if($fold)
        echo $fold->getMetaDesc();
    ?>@endsection

@section("og_image")<?php
    if($fold)
        echo $fold->getThumbInImageListWithNoImg();
    ?>@endsection
@section("title")<?php
if($strSearch)
    echo "Tim kiem: $strSearch | ".\App\Models\SiteMng::getTitle();
else{
    if($fold)
        echo $fold->name . " | " .\App\Models\SiteMng::getTitle();
    else
        echo "Sản phẩm: " .\App\Models\SiteMng::getTitle();
    }
    ?>@endsection

@section("css")
    <style>
        .brc_path {
            /*font-weight: bold;*/
            display: inline-block;
        }
        .brc {
            margin-bottom: 15px;
        }
    </style>
@endsection

@section("content")

    <?php
    $page = request('page');
    if(!$page)
        $page = 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;



    ?>
    <div class="container pt-0 " style="min-height: 400px">
        <div class="brc2 qqqq1111 my-3" data-code-pos="ppp1690297717529">
            <i class="fas fa-table"></i>

            <a href="/san-pham"> Sản phẩm / </a>

            <?php
            \App\Models\BlockUi::showEditLink_($fold?->getEditLinkAdm());
            echo $brc ? $brc : "";
            ?>
        </div>

        <div class="row">
            <div class="col-0" data-code-pos='ppp16897676781191'>

            </div>
            <div class="col-12" data-code-pos='ppp16897676757731'>


                <div class="row product_row1">
                    <?php
                    if ($fid){
                        $tt = \App\Models\Product::where(
                            function($qr) use ($fid){
                                $qr->where("parent_id", $fid)->orWhereRaw("(MATCH parent_all AGAINST (? IN BOOLEAN MODE))", $fid);
                            }
                        )->where('status' ,1);
                        $mm = \App\Models\Product::where(
                            function($qr) use ($fid){
                                $qr->where("parent_id", $fid)->orWhereRaw("(MATCH parent_all AGAINST (? IN BOOLEAN MODE))", $fid);
                            }
                        )->where("status", 1)->orderByDesc('created_at')->offset($offset)->limit($limit);
                        if($strSearch){
                            $tt = $tt->where('name', 'LIKE', "%$strSearch%");
                            $mm = $mm->where('name', 'LIKE', "%$strSearch%");
                        }
                    }
                    else{
                        $tt = \App\Models\Product::where('status' ,1);
                        $mm = \App\Models\Product::where("status", 1)->offset($offset)->orderByDesc('created_at')->limit($limit);
                        if($strSearch){
                            $tt = $tt->where('name', 'LIKE', "%$strSearch%");
                            $mm = $mm->where('name', 'LIKE', "%$strSearch%");
                        }
                    }
                    $tt = $tt->count();
                    $mm = $mm->get();

                    //for($i =0; $i<10; $i++)
                    foreach ($mm AS $obj)
                    {
                    if ($obj instanceof \App\Models\Product) ;
                    $img = $obj->getThumbInImageListWithNoImg();
                    $link = $obj->getLinkPublic();
                    ?>
                    <div data-code-pos='ppp17060656975361'class=" col-6 col-md-4 col-lg-3 col-xl-3 one-product txt-center">
                        <a href="<?php echo $link  ?>">
                            <div>
                                <button class="btn d-none"> +</button>
                                <img src="<?php echo $img ?>" class="img-fluid">
                            </div>
                            <span class="">
                                {{$obj->name}}
                    </span>

                            <span style="color: brown">
                            Giá: <?php
                            if(!$obj->price)
                                echo "Liên hệ";
                            else{
                                echo number_formatvn0($obj->price) . ' VND ';

                                if($obj->price1){
                                    echo "\n &nbsp <span style='text-decoration: line-through; display: inline-block'>";
                                    echo number_formatvn0($obj->price1) . '  <span>';
                                }

                            }
                            ?>

                            </span>
                        </a>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                <?php
                echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyle(null, $tt, $limit, $page);
                ?>
            </div>
        </div>
    </div>
@endsection

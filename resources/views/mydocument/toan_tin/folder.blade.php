@extends(getLayoutNameMultiReturnDefaultIfNull())

<style>
    .head_pid {
        /*font-weight: bold;*/
    }
    .row1 {
        border-bottom: 2px solid darkorange;
    }
    select.head_pid {
        width: 100%;
        background-color: #eee;

    }

    select.head_pid:has(> option.selected){
        color: darkorange;
        font-weight: bolder;
    }

    .heading1 {
        background-color: darkorange;
        color: white;
        display: inline-block;
        font-weight: bold;
        padding: 7px 30px 7px 15px;
        font-size: 20px;
        text-transform: uppercase;
    }

    .paginator a {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 3px 5px;
        background-color: white;
        min-width: 25px;
        text-align: center;
        display: inline-block;
        margin-right: 5px;
        font-size: small;

    }
    .menu a.active {
        font-weight: bold;
        color: red;
    }
    .menu .head_pid {
        border: 1px solid #ccc;
        /*background-color: #ccc;*/
        padding: 10px 10px;
        border-radius: 5px;
        /*font-size: small;*/
        margin: 5px 0px 5px 0px;

    }
    .paginator a.active {
        background-color: royalblue;
        color: white;
    }
    .paginator select {
        padding: 4px;
        border-radius: 5px;
        border-color: #ccc;
        font-size: small;
    }
    .paginator {
        margin: 20px 0px 25px 0px;
        text-align: center;
    }

    .paginator .pg_total{
        font-size: small;
        padding: 5px;
        border: 1px solid #eee;
    }

</style>

<?php
$title = "Tất cả sách giáo khoa";
$mFid = [];
if ($fid0 = $fid = request('fid')){
    $fid0 = $fid = trim($fid,',');
    $mFid = explode(',', $fid);

    $title = '';
    if(is_numeric($fid)){
        if ($obj = \App\Models\MyDocumentCat::find($fid))
            $title = $obj->name;
    }
    else{
        $mFid = array_filter($mFid);
        foreach ($mFid AS $folderId){
            if($fod = \App\Models\MyDocumentCat::find($folderId)){
                $title .= " + $fod->name ";
            }
        }
    }
}

$title = trim($title);
$title = trim($title, '+');

?>
@section('title')
    {{$title}}
@endsection

@section('content')
    <?php
    if(!($obj ?? ''))
//        goto __END;
        ?>

    <div class="container py-3" style="min-height: 500px">

        <div class="row">
            <div data-code-pos="qqq1706686254945" class="col-sm-3 txt-center  menu">
                    <?php
                $fidX = ','.$fid0.',';
                $mm = \App\Models\MyDocumentCat::where('parent_id', 0)->orderBy("orders", 'asc')->get();
                $link = \LadLib\Common\UrlHelper1::getUrlRequestUri();
                //All folder root

                foreach ($mm AS $obj){
                    $m1 = \App\Models\MyDocumentCat::where('parent_id', $obj->id)->orderBy("orders", 'asc')->get();
                    $found = '';
                    foreach ($m1 AS $obj1) {
                        if(strstr($fidX, ",$obj1->id,") !== false){
                            $found = $obj1->id;
                            break;
                        }
                    }

                    $fidRemove = str_replace(",$found,",",", $fidX);
                    $fidRemove = trim($fidRemove, ',');
                    $linkRemove = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('fid', $fidRemove);
                    $linkRemove = str_replace("%2C", ',',$linkRemove);
                    echo "<select onchange='window.location.href=this.value' class='head_pid'> ";
                    echo "\n<option value='$linkRemove'>--- Tất cả  $obj->name --- </option>";

//                        echo "<br/>\n Found = $found, fidX = $fidX";

                    //Tim all subfolder
                    foreach ($m1 AS $obj1) {
                        if($found)
                            $fidOK = str_replace(",$found,",",$obj1->id,", $fidX);
                        else
                            $fidOK = $fid0.",$obj1->id" ;
                        $fidOK = trim($fidOK ,',');
                        $mFidOK = explode(',', $fidOK);
//                        sort($mFidOK);
                        $fidOK = implode(",", $mFidOK);
                        $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('fid', $fidOK);
                        $link = str_replace("%2C", ',',$link);
                        $selected = null;
                        if(in_array($obj1->id, $mFid)) {
                            $selected = 'selected';
                        }
                        echo "<option class='$selected' $selected value='$link' title='$obj1->id'>   $obj1->name  </option> ";
                    }
                    echo "\n </select>";
                }
                ?>
            </div>
            <div class="col-sm-9 bg-light" data-code-pos="qqq1706686248545">
                <h1 style='font-size: 20px;' class='my-3'>
                    <?php
                    echo " $title ";
                    if (isSupperAdmin_()) {
                        echo "\n <a href='/admin/my-document-cat/edit/$fid' target='_blank'> <i class='fa fa-edit'></i> </a>";
                    }
                    ?>
                </h1>
                <hr>
                <?php
                $uri = \LadLib\Common\UrlHelper1::getUrlRequestUri();
                $cPage = request('page');
                if (!is_numeric($cPage) || $cPage <= 0)
                    $cPage = 0;
//                $cPage--;
//                if($cPage < 0)
//                    $cPage = 0;
                $limit = 15;
                $offset = ($cPage - 1) * $limit;
                if($offset< 0)
                    $offset = 0;
                $total = 0;
                $mDoc = null;
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($mFid);
//                echo "</pre>";
                if(count($mFid) <= 1){
                    if(!$fid){
                        $total =  \App\Models\MyDocument::whereRaw("  1 ");
                        $mDoc = \App\Models\MyDocument::whereRaw(" 1 ");
                    }else{
                        $total =  \App\Models\MyDocument::whereRaw(" MATCH parent_all AGAINST (? IN BOOLEAN MODE)", $fid);
                        $mDoc = \App\Models\MyDocument::whereRaw("MATCH parent_all AGAINST (? IN BOOLEAN MODE)", $fid);
                    }
                }
                else{
                    foreach ($mFid AS $fid){
                        if(!$mDoc)
                            $mDoc = \App\Models\MyDocument::whereRaw("MATCH parent_all AGAINST (? IN BOOLEAN MODE)", [$fid]);
                        else
                            $mDoc = $mDoc->whereRaw("MATCH parent_all AGAINST (? IN BOOLEAN MODE)", [$fid]);
                        if(!$total)
                            $total =  \App\Models\MyDocument::whereRaw(" MATCH parent_all AGAINST (? IN BOOLEAN MODE)", [$fid]);
                        else
                            $total =  $total->whereRaw(" MATCH parent_all AGAINST (? IN BOOLEAN MODE)", [$fid]);
                    }
                }
                if(!$mDoc || !$mDoc->count())
                    tb("Chưa có nội dung");

                $mDoc = $mDoc->skip($offset)->limit(21)->get();
                $total = $total->count();
                $ttPage = ceil($total / $limit);
                echo "<div class='paginator'>";
                $str = clsPaginator::getPaginatorString($uri, $ttPage , $cPage, $limit , $total, 5);
                echo "$str ";
                echo "</div>";

                ?>

                <div data-code-pos="qqq1706686245741" class="row row-cols-2 row-cols-md-3 g-4">
                    <?php




//                    $mDoc = \App\Models\MyDocument::where(['parent_id' => $obj->id])->where(function ($query) {
////                        $query->where('name', 'like', "%toán%")->orWhere('name', 'like', "%tin học%");
//                    })->limit(50)->get();

                    $dmx = 0;
                    foreach ($mDoc AS $doc){
                        $dmx++;
                        if ($dmx > 5)
                            $dmx = 1;
                        if ($doc instanceof \App\Models\ModelGlxBase) ;
                        $img = $doc->getThumbSmall(300, 'https://toantin' . $dmx . ".mytree.vn");
                        if(!$img)
                            continue;

                        if(!$doc->file_list)
                            continue;

                        ?>

                    <div class="col d-flex align-items-stretch">
                        <div class="card">
                            <a href="/tai-lieu/chi-tiet?fid={{$doc->getId()}}">
                                <img src="{{$img}}" class="placeholder1 card-img-top"
                                     alt="{{$doc->name}}"/>
                                <div class="card-body">
                                    <h5 class="card-title">{{$doc->name}}</h5>
                                    <p class="card-text">

                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>

                        <?php
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <?php
    __END:
    ?>
    <p></p>
    <p></p>
@endsection

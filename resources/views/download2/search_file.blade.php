<?php

use App\Models\OrderItem;
use App\Models\CloudServer_Meta;
use App\Models\DownloadLog;
use App\Models\TmpDownloadSession;
use App\Models\FileCloud;
use App\Models\FileRefer;
use App\Models\FileUpload;
use App\Models\Product;
use App\Models\User;
use Base\ModelCloudServer;
use Illuminate\Support\Facades\Auth;

$searchString = $_GET['search_string'] ?? '';
$searchString = strip_tags($searchString);
if($searchString){
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $siteId = \App\Models\SiteMng::getSiteId();
    $uid = getCurrentUserId();
    if($uid)
        outputT("/var/glx/weblog/search_file.$siteId.uid.log", "IP $ipAddress, UID = $uid, search_string = $searchString");
    else
        outputT("/var/glx/weblog/search_file.$siteId.log", "IP $ipAddress, UID = $uid, search_string = $searchString");
}
?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('css')
<style>

    a.one_file_name {
        display: inline-block;
        max-width: 600px; /* Đặt giới hạn chiều rộng tương ứng với số ký tự bạn muốn hiển thị */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; /* Thêm dấu ba chấm nếu văn bản vượt quá giới hạn */
    }
    .search_4s_type{
        border: 1px solid #ccc;
        padding: 5px;
        display: inline-block;
    }
    .sl_file_size {
        /*margin-left: 12px;*/
        /*color: red;*/
        /*border-radius: 5px;*/
        padding: 3px;
        border: 1px solid #ddd;
    }
    .div_ext_search {
        padding: 5px;
        margin: 5px;
        border: 1px solid #eee;
        font-size: small;
        margin: 10px auto 0px auto;
        max-width: 1000px;
        background-color: beige;
        border-radius: 5px;
    }
    .ext_file_name {
        display: inline-block;
        font-size: small;
        border: 1px solid #ccc;
        padding: 1px 5px;
        margin: 3px 3px 2px 0px;
    }
    .selecting {
        background-color: firebrick;
        color: white !important;
        display: inline-block;
        border-radius: 3px;
    }
    .card-body {
        padding-top: 20px!important;
    }
    .paginator_glx .link_pg i{
        margin-top: 1px;
    }
    .paginator_glx .link_pg {
        /*vertical-align: top;*/
        min-width: 35px;
        display: inline-block;
        border: 1px solid #ccc;
        padding: 3px 5px;
        border-radius: 5px;
        text-align: center;
        margin: 3px 3px;
        /*height: 40px!important;*/
    }
    .form_search input.form_search_input {
        /*color: ;*/
        font-weight: bold;
        min-width: 335px;
        padding: 6px 10px;
        /*border-radius: 10px 0px 0px 10px;*/
        border: 1px solid #ccc;
    }
    @media only screen and (max-width: 600px) {
        .form_search input.form_search_input {
            color: red;
            min-width: 150px;
            /*padding: 4px 10px;*/
            /*border-radius: 10px 0px 0px 10px;*/
            border: 1px solid #ccc;
        }
    }
    .center {
        text-align: center;
    }
    .card {
    }
    .pagination {
        display: inline-block;
    }
    .pagination a {
        color: black;
        /*float: left;*/
        padding: 3px 6px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        margin: 0 4px;
    }
    .pagination a.active {
        background-color: gray;
        color: white;
        border: 1px solid gray;
    }
    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }
    .search-form {
        margin: 0px;
    }
    .search_zone .card{
        /*border: 1px solid #ccc;*/
        border-radius: 0px!important;
        margin-bottom: 20px;
    }
    .search_zone a{
        color: #686868;
    }
    .paginator_glx span {
        font-size: 80%
    }
    .paginator_glx a {
        font-size: 80%
    }
    .pg_selecting {
        background-color: orange;
        color: white!important;
    }
</style>
@endsection

@section('meta-description')
    <?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('meta-keywords')
    <?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('title')
Tìm file:  {{ $searchString ??''}}
@endsection

@section('content')
    <?php
    use LadLib\Common\UrlHelper1;

    $params = request()->all();
    $searchString = '';
    if (isset($params['search_string'])) {
        $params['search_string'] = substr($params['search_string'], 0, 100);
        $searchString = $params['search_string'];
        $searchString = trim(urldecode($searchString));
    }
    $searchString = str_replace(['`', '!', '@', '^', ",", '.', ':', "_", "#", "-", "(", ")", "{", "}", "+", "*"], ' ', $searchString);

//    $arrBlackWord = U4sHelper::getBlackWordList();
//    if (in_array($searchString, $arrBlackWord)) {
//        return null;
//    }


    if (isset($params['limit']) && is_numeric($params['limit']))
        $limit = $params['limit'];
    else
        $limit = $params['limit'] = 30;

    $cPage = 1;
    if (isset($params['page']))
        $cPage = $params['page'];
    if(!$cPage || $cPage <= 0 || !is_numeric($cPage))
        $cPage = $params['page'] = 1;


    ///
    $obj = new \App\Models\MyDocument();

    $dbName = $obj->getElasticDbName();
    $prs = getParamForElastic($searchString, $params, $dbName);

    try{
        $response = searchElastic($prs, $dbName);
    } catch (\Throwable $e) { // For PHP 7
        $strRet = ($e->getMessage());
        rtErrorApi("ErrorEl2: " . $strRet);
        return;
    } catch (\Exception $exception) {
        $strRet = ($exception->getMessage());
        rtErrorApi("ErrorEl1: " . $strRet);
        return;
    }

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($response);
//echo "</pre>";
    $total = 0;
    $ret = [];
    if (!$response) {
        $strRet = ("Not found result");
        ol3($strRet);
        rtErrorApi($strRet);
    } else {
        if (isset($response['hits']['hits'])) {
            $total = $response['hits']['total']['value'];
        }
        foreach ($response['hits']['hits'] as $hit) {
            $name = $hit['_source']['name'];
            if (isset($hit['_source']['summary']))
                $sum = $hit['_source']['summary'];
            //$cont = $hit['_source']['content'];
            $id = $hit['_id'];
            //echo "<br/>\n $id . $name  ";
            if($obj0 = \App\Models\MyDocument::find($id))
            {
                $obj = $obj0->toArray();
                $ret[] = (object) $obj;
            }
        }
    }

    $nPage = ceil($total / $limit);

    ////////////////////////////// Show ket qua //////////////////////////////
    $format_as_VietMediaF = 0;
    if (isset($_GET['format_as']) && ($_GET['format_as'] == 'vietmediaf1' || $_GET['format_as'] == 'vietmediaf2')){
        ob_clean();
        $format_as_VietMediaF = 1;
    }

    $mRet = [];
    $cc = 0;


    ?>


    <div class="container pt-0 my-1" style=" border: 0px solid #ccc">



    </div>

    <div class="container p-0">
    <div class="row search_zone">
        <div class="col-12 my-4">
            <div class="card">
                <div class="card-header" style="">


                    <form class="form_search_1" action="/search" style="margin-right: 20px; " data-code-pos='ppp17388198896471'>
                        <input data-code-pos="ppp17364869693501" placeholder="Tìm, ví dụ: toán, tin, bài tập..." name="search_string" value="{{ request('search_string')  }}"
                               class="search-top" style="font-size: 14px; " type="text"><button type="submit" class="search-top" style="">
                            <input type="hidden" name="exactly" value="1">
                            <input type="hidden" name="sort_by" value="">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>


                    <span style="display: inline-block; vertical-align: middle; ">
                    Tìm thấy <b> {{$total}} </b> kết quả
</span>
                    <div class="pt-2 text-small" style="font-size: small">
                    Hướng dẫn: Tìm nhiều từ, ví dụ : "<u>Toán sáng</u>" , có thể tìm ra sách <u>Toán</u> trong bộ "Trân trời <u>Sáng</u> tạo". Có thể tìm có hoặc không dấu.
                    </div>
                </div>


                <div class="card-body">


                    <div class="mb-4  mt-2">
                    <?php

                    $current_page = $cPage;
//                                        echo "<br/>\n $total_page, $current_page";
                    echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyle(null,$total, $limit, $current_page, 5, 5);

                    ?>
                    </div>
                    <?php
                    ////////////////////////////////////////////////////
                    ///
                    ///
                    ///


                    $soThuTu = $current_page * $limit - $limit;
                    if($ret)
                    foreach ($ret as $obj) {
                        $soThuTu++;
                        $link = "/tai-lieu/chi-tiet?fid=" . qqgetRandFromId_($obj->id);
                        ?>


                    <div style="padding:  10px  3px 10px 3px; font-weight: bold; border-top: 1px solid #ccc"> <a href='{{$link}}'> {{$soThuTu }}. {{$obj->name}}  </a>  </div>
                        <?php


//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($objDb->toArray());
//        echo "</pre>";

                    }
                    ?>

                </div>
                <?php
                    _END1:
                ?>
            </div>

        </div>

    </div>
    </div>

@endsection

@section('title')
    Tìm file: {{ $searchString ??''}}
@endsection

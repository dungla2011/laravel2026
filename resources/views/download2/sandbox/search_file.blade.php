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
        border: 1px solid #ccc;
        display: inline-block;
        padding: 3px 5px;
        border-radius: 5px;
        text-align: center;
        margin: 0px 3px;
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


    $params = $_GET;

    $padExactlyStyle2 = '';$padExactlyStyle1 = '';
    if (isset($params['exactly']) && $params['exactly']) {
        $padExactlyStyle1 = 'color: orange; font-weight: bold; border: 1px solid orange';
    } else
        $padExactlyStyle2 = 'color: orange; font-weight: bold; border: 1px solid orange';

    $mSize = [
        10 => "10 MB",
        50 => "50 MB",
        100 => "100 MB",
        500 => "500 MB",
        1024 => "1 GB",
        1024 * 2 => "2 GB",
        1024 * 3 => "3 GB",
        1024 * 5 => "5 GB",
        1024 * 10 => "10 GB",
        1024 * 20 => "20 GB",
        1024 * 30 => "30 GB",
        1024 * 50 => "50 GB",
        1024 * 100 => "100 GB",
        1024 * 200 => "200 GB",
        1024 * 500 => "500 GB",
    ];

    ?>


    <div class="container pt-0 my-1" style=" border: 0px solid #ccc">

        <?php
        ////////////////////////////////////////////////////

        $domain = UrlHelper1::getDomainHostName();
        $link = "https://$domain/api/v1?cmd=search_file_name";
        $uri = \LadLib\Common\UrlHelper1::getUrlRequestUri();
        $uri = explode("?", $uri)[1] ?? '';
//        echo "\n$uri";
        $link1 = $link . "&$uri";

        if(isDebugIp()){
//            bl($link1);
        }

        $time = time();
//        echo "<br/>\n $link1";;
        $ret = file_get_contents($link1);

        $dtime = time() - $time;
        $foundRet = 0;
        if(!$ret){
//            echo "Không tìm thấy file nào";
            $foundRet = 0;

        }
//        echo "\n Time = $dtime , $link1";
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($ret);
//        echo "</pre>";
//
//        return;
        $ret1 = json_decode($ret);

        if($ret1)
        if($ret1->errorNumber < 0){
            echo "<pre>";
            print_r($ret1);
            echo "</pre>";
            die();
        }

        if(!$ret1 || !$ret1->payload){
//            echo "Không tìm thấy file nào";
            $foundRet = 0;
        }

        if(!($ret1->payload ?? '')){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($ret1);
//            echo "</pre>";
//            die();
        }

        if($ret) {
            $mLink = $ret1->payload->links;
            $found = $ret1->payload->found;
            $limit = $ret1->payload->limit;
            $total_page = $ret1->payload->total_page;
            $current_page = $ret1->payload->current_page;
            $foundRet = 1;
        }

        ?>

    </div>

    <div class="container p-0">
    <div class="row search_zone">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div style="text-align: center">
                        <form id="form_search" method="get" action="/search-file?exactly=1" name="form_search" style=""
                              class="form_search mt-2">

                            <div class="" style=" margin: 0 auto; font-size: small; background-color: ; width: 460px">
                                <div class="mb-2">
                                    <input type="text" style="width: 326px" class="form_search_input" placeholder="Nhập tên file tìm kiếm..."
                                           name="search_string" value="<?php echo $searchString ?>"><button type="submit" class=""
                                            style=" display: inline-block; border-radius; width: 80px  ;  border: 0px; background-color: orange; color: white; padding: 7px 10px">
                                        Tìm
                                    </button>
                                    <input type='hidden' name='exactly' value='1'/>
                                    <input type='hidden' name='sort_by' value='new'/>

                                </div>

                                <div style="margin-top: 0px">
                                    <!--                    >-->
                                    <a class="search_4s_type" href="<?php echo UrlHelper1::setUrlParamThisUrl("exactly", 1) ?>"
                                       style="padding: 3px 10px; font-weight: normal; margin-left: 1px; margin-right: 10px ;  <?php echo $padExactlyStyle1 ?>"
                                       title="Tìm tên file có chứa chính xác các từ tìm kiếm, mỗi từ cách nhau 1 dấu cách trong tên file">
                                        Tìm chính xác</a>

                                    <a class="search_4s_type" href="<?php echo UrlHelper1::setUrlParamThisUrl("exactly", null) ?>"
                                       style="font-weight: normal;margin-left: 1px; margin-right: 10px ; padding: 3px 10px;; <?php echo $padExactlyStyle2 ?>"
                                       title="Tìm tên file gần đúng với chuỗi tìm kiếm">Tìm gần đúng</a>

                                    <?php
                                    foreach ($params as $name => $value) {
                                        if ($name == 'search_string' || $name == 'fbclid' || $name == 'sort_by' || $name == 'ext'
                                            || $name == 'exactly' || $name == 'page' || $name == 'from_size' || $name == 'to_size'
                                        )
                                            continue;
                                        $name = htmlspecialchars($name);
                                        $value = htmlspecialchars($value);
                                        $value = trim($value);
                                        if ($value)
                                            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                                    }

//                    if (isAdminEmail())
                                    if (1)
                                    {
                                        ?>

                                    <div style="display: inline-block; margin-top: 5px; font-size: small">
                                        <select name="from_size" style="width: 80px" class="sl_file_size" <?php
                                                                                          if (isset($params['from_size']) && ($params['from_size'])) {
                                                                                              echo " style='color:orange' ";
                                                                                          }
                                                                                          ?>>
                                            <option value='0'> - Từ MB -</option>
                                                <?php
                                                foreach ($mSize as $size => $txt) {

                                                    $pad = '';
                                                    if (isset($params['from_size']) && is_numeric($params['from_size']) && $params['from_size'] == $size)
                                                        $pad = " selected ";

                                                    echo "<option value='$size' $pad>Từ $txt</option>";
                                                }
                                                ?>
                                        </select>
                                        <select name="to_size" class="sl_file_size" style="width: 80px; margin-left: 10px" <?php
                                                                                        if (isset($params['to_size']) && ($params['to_size'])) {
                                                                                            echo " style='color:red' ";
                                                                                        }
                                                                                        ?>>
                                            <option value='0'> - Đến -</option>
                                                <?php
                                                foreach ($mSize as $size => $txt) {

                                                    $pad = '';
                                                    if (isset($params['to_size']) && is_numeric($params['to_size']) && $params['to_size'] == $size)
                                                        $pad = " selected ";
                                                    echo "<option value='$size' $pad>Đến $txt</option>";
                                                }
                                                ?>
                                        </select>
                                            <?php
                                            if ((isset($params['from_size']) && is_numeric($params['from_size']) && $params['from_size'] > 0)
                                                || (isset($params['to_size']) && is_numeric($params['to_size']) && $params['to_size'] > 0)
                                            ) {
                                                $linkClearSize = UrlHelper1::setUrlParam(null, 'from_size', null);
                                                $linkClearSize = UrlHelper1::setUrlParam($linkClearSize, 'to_size', null);
                                                echo " <a title='Bỏ chọn Size - Clear Size Option' style='color: red' href='$linkClearSize' class='fa fa-times'> </a>";
                                            }
                                            ?>
                                    </div>
                                </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            //    if(isAdminEmail())
                            if (0) {
                                $mExt = ['doc', 'docx', 'pdf', 'xls', 'iso', 'mp3', 'mp4', 'mkv', 'ts', 'rar', 'zip', 'm4v', '7z', 'txt', 'srt', "wmv", "avi", "flv", "gif", "png", "jpg", "mpg", "fla",
                                    "flac", "wav", "mid", "wma", "mpeg", "m4a", "mts", "m2ts", "m4v", "mov", "flv", "vob",];
                                $mExt = array_unique($mExt);
                                sort($mExt);
//                                if(0)
                                echo "\n<div class='div_ext_search' style=''>";
                                echo "\n Tìm đuôi file: ";
                                $valE = '';
                                $link1RemoveExt = UrlHelper1::setUrlParam(null, 'ext', null);
                                if (isset($params['ext']) && $params['ext']) {
                                    $valE = $params['ext'];
                                    echo "\n <a href='$link1RemoveExt' title='Bỏ chọn đuôi file này' style='font-size: 16px; color: firebrick'> <i class='fa fa-times-circle'></i> </a>";
                                }
                                echo "\n<input style='width: 50px; border: 1px solid #ccc; border-radius: 5px; padding: 1px 5px; color: red' placeholder='Nhập..' name='ext' value='$valE' >";
                                foreach ($mExt as $ext) {
                                    $link1 = UrlHelper1::setUrlParam(null, 'ext', $ext);
                                    $link1 = UrlHelper1::setUrlParam($link1, 'page', null);
                                    $padStyle = "";
                                    if (isset($params['ext']) && $params['ext'] && $params['ext'] == $ext) {
//                    $padStyle = " color: firebrick; font-weight: bold; ";
                                        echo "\n <a class='ext_file_name selecting' title='Bỏ chọn đuôi file này' style='$padStyle;' href='$link1RemoveExt'> <span style='' class='fa fa-times'></span> $ext </a> ";
                                    } else
                                        echo "\n <a class='ext_file_name' title='Chọn đuôi file này' style='$padStyle' href='$link1'> $ext </a> ";
                                }
                                echo "\n</div>";
                            }
                            ?>
                        </form>
                    </div>
                </div>

                <?php
                if(!$foundRet){
                    bl("Không tìm thấy file nào");
                    goto _END1;
                }
                ?>

                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12" >
                                <div style="text-align: center" class="dataTables_paginate paging_simple_numbers mb-4" id="example1_paginate" data-code-pos='ppp17364916136671'>
                                    <?php
//                                        echo "<br/>\n $total_page, $current_page";
                                       echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyle(null,$found, 20, $current_page, 5, 5);

                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
                                    <thead>
                                    <tr>
                                        <th class="sorting">STT</th>
                                        <th class="sorting">Tên file</th>
                                        <th class="sorting">Thể loại</th>
                                        <th class="sorting" rowspan="1">Kích thước</th>
                                        <th class="sorting" rowspan="1">Ngày</th>
{{--                                        <th class="sorting">Link</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                        $soThuTu = $current_page * $limit - $limit;

                                    foreach ($mLink AS $one){
                                        $soThuTu++;
                                        /*
                                         * [name] => Lạc ngoài không gian_S03E01_Ba chú chim nhỏ 2021 Vie NF WEB.mkv
                                                        [created_at] => 2024-05-21T17:37:19.000000Z
                                                        [file_size] => 1486303514
                                                        [link1] => ms6e585d5759565b57
                                         */
                                        $name = $one->name;
                                        $created_at =  nowy(strtotime($one->created_at));
                                        $file_size = $one->file_size;
                                        $link1 = $one->link1;
                                        $extension = pathinfo($name, PATHINFO_EXTENSION);

                                        ?>

                                    <tr>
                                        <td> {{$soThuTu}}  </td>
                                        <td>  <a title="{{$name}}" class="one_file_name" href="/f/{{$link1}}">{{$name}} </a> </td>
                                        <td> {{ $extension  }} </td>
                                        <td> {{ByteSize($file_size)}} </td>
                                        <td> {{$created_at}} </td>

                                    </tr>

                                        <?php

                                    }
                                    ?>




                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
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

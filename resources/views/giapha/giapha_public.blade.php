<?php
//if(getDomainHostName() == 'v5.mytree.vn')
{
    if(!is_numeric($_GET['pid'] ?? 0)){
        $pid = $_GET['pid'];
        if($gpx = \App\Models\GiaPha::where("id__", $pid)->first()) {
            if(strlen("$gpx->id")>=17){
                $url = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('pid', $gpx->id);
    //            die("URL = $url");
                header("Location: $url");
                die();
            }
        }
    }
}

if(isDebugIp()){
//    die($version_using);
}

use Illuminate\Support\Facades\Cache;
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(nowy() > '2023-07-03')
if (filemtime(public_path() . "/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js") > filemtime(public_path() . "/tool1/lad_tree_vn/tree_glx01.js")) {
//    die("Error: need update code!");
}
if (isset($_GET['pid']) && !$_GET['pid']) {
    bl3("Không tồn tại cây!", "<a href='/my-tree'> Trở lại</a>");
    return;
}
$treeInfo = null;
$uid = getCurrentUserId();


$isVip = 0;
if($uid){
    //Xem user đã mua VIP chưa
    if($bill = \App\Models\OrderItem::where(['user_id' => $uid])->first()){
        $isVip = 1;
    }
}


//Nếu user tạo sau tháng 11 cũng sẽ chuyển sang bản mới:




?>
    <!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title data-code-pos="ppp1676255062649">
        <?php
        $userIdTree = null;
        $title = 'Tạo cây tổ chức, cây gia phả, phả hệ';
        $domain = strtoupper(\LadLib\Common\UrlHelper1::getDomainHostName());
        if (isset($_GET['pid'])) {
            $pid = $_GET['pid'];

            if((new \App\Models\GiaPha_Meta())->isUseRandId())
            if(is_numeric($pid))
                die("Not valid PID!");

            if (!is_numeric($pid)){

                if($gpx = \App\Models\GiaPha::where("id__", $pid)->first())
                {
                    $pid = $gpx->id;

                }
                else
                    $pid = qqgetIdFromRand_($pid);
            }


            if (is_numeric($pid) && $gp = \App\Models\GiaPha::find($pid)) {

                $title = "Cây: $gp->name | " . \LadLib\Common\UrlHelper1::getDomainHostName();
                if ($treeInfo = \App\Models\MyTreeInfo::where(['tree_id' => $pid])->first()) {





                    if ($treeInfo->user_id != $gp->user_id) {

                    }
                    $meta = \App\Models\MyTreeInfo::getMetaObj();
                    $mFieldEdit = $meta->getShowEditAllowFieldList(DEF_GID_ROLE_MEMBER);
                    if ($treeInfo instanceof \App\Models\ModelGlxBase) ;

                    $userIdTree = $treeInfo->user_id;
                    unset($treeInfo->created_at);
                    unset($treeInfo->updated_at);
                    unset($treeInfo->deleted_at);
                    unset($treeInfo->user_id);
                    unset($treeInfo->status);

                    if(0) //Show ra mọi trường, ko bỏ đi cái nào
                    foreach ($treeInfo->getAttributes() AS $field => $val) {
                        if($field == 'tree_nodes_xy')
                            continue;
                        if ($field != 'id' && !in_array($field, $mFieldEdit))
                            unset($treeInfo->$field);
                    }
                    $title = $treeInfo->name . " - " . $treeInfo->title . " - " . $domain;
                    $title = str_replace("-  -", '-', $title);
                } else {
                    $treeInfo = \App\Models\MyTreeInfo::create(['name' => $gp->name, 'user_id' => $gp->user_id, 'title' => '', 'tree_id' => $gp->id]);
                }
                $treeInfo->initDefaultValue();
            }
        }

        echo $title;
        ?>
    </title>


    <?php


    if(isDebugIp()){
//        die(" OK123 $treeInfo->id / $pid");
    }

    if(isset($pid) && $pid && !$treeInfo){
        bl("Error: Không tìm thấy cây!" , "<a href='/my-tree'>Trở lại</a>");
        die();
    }

    if (isset($_GET['pid']) && $_GET['pid']) {
        $pid = $_GET['pid'];
        $pid0 = qqgetIdFromRand_($pid);
        if (!$objTree = \App\Models\GiaPha::find($pid0)) {
            bl("Không tồn tại cây!", "<a href='/my-tree'>Trở lại</a>", 100);
            return;
        }

        $version_using = 1;
        if($tmp1 = \App\Models\GiaPhaUser::where("user_id", $objTree->user_id)->first())
            $version_using = $tmp1->version_using ?? 1;

//    dump($treeInfo);

        //echo "<!-- tree_nodes_xy = $treeInfo->id | $treeInfo->tree_nodes_xy -->";

        $model = new \App\Models\GiaPha();
        $objMeta = \App\Models\GiaPha::getMetaObj();
        if($objMeta instanceof \App\Models\GiaPha_Meta);
        $objPr = new \App\Components\clsParamRequestEx();
        $objPr->set_gid = 3;

        //Không cần setUID
        //Phải set chứ nhỉ?
        if ($objTree)
            $objPr->need_set_uid = $objTree->user_id;

//
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($objPr->need_set_uid);
//    echo "</pre>";

//    $objPr->need_set_uid = null;
        $objPr->module = 'member';
        $objPr->ignore_check_userid = 1;
//$pr = ['pid' => $pid, 'include_brother' => 1, 'get_all' => 1, 'order_by' => 'orders', 'order_type' => 'DESC'];
        {
            //Lấy thêm vợ chồng
            $pr = ['pid' => $pid, 'get_tree_all' => 1, 'order_by' => 'orders', 'order_type' => 'DESC'];
        }



        try {

            $t1 = microtime(1);

//            if(isIPDebug()){
//
//                $mretAll = [];
////getTreeDeep('sa989197', $objPr, $mmAll);
//                \App\Models\GiaPha_Meta::getTreeDeep($pid, $objPr, $mretAll, 0);
////                die('xxxx');
//            }

            function escapeXmlSpecialChars($string) {
                $search = ['&', '<', '>', '"', "'"];
                $replace = ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'];
                return str_replace($search, $replace, $string);
            }

            $keyCache = $objMeta->getCacheKeyPublic($pid);
            $keyCacheTime = $objMeta->getCacheKeyPublicTimeCreated($pid);

            $timeCacheThisTree = null;
            if (Cache::has($keyCacheTime) && $timeCacheThisTree = Cache::get($keyCacheTime)) {
            }
            //nếu cây này thuộc về user:
//    if ($objTree->user_id == auth()->id())
//    //if(isset($_POST['clear_cache_this_url']))
//    {

//    }
//Cache::forget($key);
//        Cache::flush();
            $needUpdateThisTreeCache = 0;
            if (1 && Cache::has($keyCache) && $mretAll = Cache::get($keyCache)) {



                //Tìm tất cả các link_remote xem có thay đổi Sau Tree này ko, nếu có thì tree này sẽ cần update lại:
                if($timeCacheThisTree)
                    foreach ($mretAll AS &$objx){

                        foreach ($objx AS $k=>$v){
                            if($v == 'null')
                                $objx[$k] = '';
                        }

//                        if(isDebugIp())
                        {

                            $objx['name'] =  escapeXmlSpecialChars($objx['name']);

//                            $objx['name'] = str_replace(['"', "*", '\''], '', $objx['name']);
                        }

                        if($objx['link_remote']){
                            $mLinkRm = explode(",", $objx['link_remote']);
                            foreach ($mLinkRm AS $lrm){
                                $keyCacheRemote = $objMeta->getCacheKeyPublicTimeCreated($lrm);
                                $timeTreeRemoteCache = null;
                                if (Cache::has($keyCacheRemote) && $timeTreeRemoteCache = Cache::get($keyCacheRemote)) {
                                    if($timeTreeRemoteCache > $timeCacheThisTree){
                                        $needUpdateThisTreeCache = 1;
                                        break;
                                    }
                                }
                            }
                        }
                    }
            } else{
                $needUpdateThisTreeCache = 1;
            }

            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($mretAll);
//                echo "</pre>";
            }
            if(request('no_cache'))
                $needUpdateThisTreeCache = 1;

            if(!$needUpdateThisTreeCache){
                echo "<!--GetCache-->";
            }
            else
            {
                echo "<!--NotGetCache-->";

//            $objPr = new \App\Components\clsParamRequestEx();
//            $objPr->set_gid = 3;

//Không cần setUID
//Phải set chứ nhỉ?
                $mretAll = [];
//getTreeDeep('sa989197', $objPr, $mmAll);

                if(isDebugIp()){
                    $t0 = microtime(1);
                }
                ladDebug::addTime(__FILE__, __LINE__);

                \App\Models\GiaPha_Meta::getTreeDeep($pid, $objPr, $mretAll, 0);

                ladDebug::addTime(__FILE__, __LINE__);

                if(isDebugIp()){

//                    echo ladDebug::dumpDebugTime();

//                    die("Time getTreeDeep: " . (microtime(1) - $t0) . " s");
                }

                Cache::put($keyCache, $mretAll, 360 * 60);
                Cache::put($keyCacheTime, time(), 360 * 60);
            }

        } catch (Throwable $e) { // For PHP 7
            bl("Có lỗi: " . $e->getMessage() . "<br>" . substr($e->getTraceAsString(), 0, 1000) . "....");
        } catch (Exception $e) {
            bl("Có lỗi2: " . $e->getMessage() . "<br>" . substr($e->getTraceAsString(), 0, 1000) . "....");
        }
    }

    if(isDebugIp()){

//        echo " xx <pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($treeInfo->toArray());
//        echo "</pre>";
//        die(" OK $treeInfo->id / $pid");
    }
    //
    //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //    print_r($treeInfo->toArray());
    //    echo "</pre>";
    ?>

    <meta name="description" content="Cây tổ chức, cây gia phả : <?php echo $title ?> ">
    <meta name="keywords" content="MyTree, phần mềm tạo phả hệ online, gia phả online, sơ đồ tổ chức online">
    <meta property="og:image" content="/images/logo/mytree1-square.png?v=1">
    <meta property="og:title" content="<?php echo $title?>">
    <meta property="og:description" content="Cây tổ chức, cây gia phả : <?php echo $title ?> ">

    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/vendor/font-awesome/font-awesome4.css">
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/tool1/lad_tree_vn/clsTreeTopDown.css?v=<?php echo filemtime(public_path() . "/tool1/lad_tree_vn/clsTreeTopDown.css") ?>">

    <script src="/vendor/jquery/jquery-3.6.0.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/vendor/popper.min.js"></script>
    <script src="/vendor/bootstrap4/bootstrap.min.js"></script>
    <script src="/assert/library_ex/js/domti.js?v=3"></script>
    <script src="/assert/library_ex/js/fsv.js"></script>
    <script src="/assert/library_ex/js/svgpz.js"></script>
    <script src="/assert/library_ex/js/hmer.js"></script>
    <script src="/vendor/lazysizes.min.js"></script>
    <script src="/vendor/galaxy/lib_base.js?v=1"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/library_ex/js/jquery-image-upload-resizer.js"></script>
    <script src="/assert/library_ex/jquery-ui/jquery.ui.position.js"></script>

    <script src="/vendor/toastr/toastr.min.js"></script>
    <script src="/admins/toast-show.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/save-svg-as-png@1.4.17/lib/saveSvgAsPng.min.js"></script>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>--}}
{{--    <script src="/assert/js/svgToPdf.js"></script>--}}

    {{--    <script src="/assert/js/circletype.min.js"></script>--}}

    {{--    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>--}}

    <style>

        .fcta-zalo-vi-tri-nut{
            bottom: 100px!important;
        }
        span.node_name_one {

            <?php
            if(isset($treeInfo))
                echo "font-size: " . $treeInfo->getFontSizeNode() . "px";
            ?>

        }

    </style>

    <?php

        $useNewVer = 0;
        $emailTree = '';


//        if(isDebugIp())
        {
            $objUserTree = \App\Models\User::find($userIdTree);
            if($objUserTree)
                $emailTree = $objUserTree->email;
        }

        $applyNewVer = 0;
        //Ngay 26.11.24
        if($objUserTree && $version_using == 2)
//        if($objUserTree->created_at > "2024-11-01")
        {
            $applyNewVer = 1;
        }

//        dump("Email tree", $emailTree);

//
//    if(\App\Models\User::isSupperAdmin()
//        || in_array($emailTree,


//    if(in_array(getUserEmailCurrent_(),$mmDemoUser)
//        //Neu cay phien ban moi:
//        || in_array($emailTree,$mmDemoUser)
//    || $applyNewVer
//    )

    if($applyNewVer)
    {

        $useNewVer = 1;
        $padx = null;
        if(\App\Models\User::isSupperAdmin())
            $padx = 'adm_';

    ?>
    <script
        src="/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001v02.js?v=<?php echo $padx . '.' . filemtime(public_path() . "/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001v02.js") ?>"></script>
    <?php
    }else{
    ?>
    <script
        src="/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js?v=<?php echo filemtime(public_path() . "/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js") ?>"></script>
    <?php
    }
    ?>


    <style>
        .menu_top {
            font-size: 105%;
        }
        @media only screen and (max-width: 600px) {
            .menu_top {
                font-size: 60%;
            }
        }
        .svg_cont_node_cls {
            z-index: 1000;
        }
        .link_line {
            z-index: 100;
        }

        .btn_ctrl_svg2{
            border: 0px solid gray;
            min-width: 120px;
            height: 25px;
            position:absolute;
            opacity:0.85;
            font-size: larger;
        }

        #selecting_nodes{
            cursor: pointer;
            font-weight: bold;
            font-size: 80%;
            border-radius: 5px;
            background-color: red;
            display: none;
            color: white;
            position: relative;
        }

        #selecting_nodes span{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

    </style>

</head>


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z364LBZ1KY"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'G-Z364LBZ1KY');
</script>

<body>
<div class="menu_div" data-code-pos="qqq1710205740380">
    <?php
    if(\LadLib\Common\UrlHelper1::getDomainHostName() == 'mytree.vn')
    {
        ?>
    <a href="/">
        {{--        <img class="svg_home" style="width: 20px" src="/assert/Ionicons/src/home.svg" alt="">--}}
        <img src="/images/logo/mytree1.png" style="height: 25px; padding-bottom: 2px; margin-right: 10px" alt="">
        {{--        Trang Chủ--}}
    </a>
    <?php
    }
    else{
        echo  " <a href='/'> <i class='fa fa-home'></i>  Trang chủ</a> ";
    }
    ?>
    <div class="menu_top" style="float: right; ">

        <?php
        if(\LadLib\Common\UrlHelper1::getDomainHostName() == 'mytree.vn')
        {
        ?>
        <span style="" id="taiTro1" >
            <i class="fa fa-usd"></i>
            <a href="/buy-vip">Mua gói VIP</a>
        </span> |
        <?php
        }
        ?>

        <a href="/my-tree" style="">
{{--            <img class="svg_home" style="width: 20px; " src="/assert/Ionicons/src/tree_9.svg" alt="">--}}

            Tạo cây</a> |
        <a target="_blank" href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2"
           style="margin-right: 5px">
 Hướng dẫn </a>

        <?php
        $email = getUserEmailCurrent_();
        if($email){
        ?>
        <a title="<?php echo $email ?>" href="/member" title="Tài khoản" style="">
            <img class="svg_home" style="width: 22px" src="/assert/seekicon.com/user_1.svg" alt="">
            <?php
            echo substr(getUserEmailCurrent_(), 0, 3) . '..';
            ?>
        </a>

        <?php
        }else{
        ?>

        <script>//window.location = "/login";</script>

        <a href="/login" title="Login" style="float: right">
            | <img class="svg_home" style="width: 22px" src="/assert/Ionicons/src/log-in.svg" alt=""> Đăng nhập
        </a>
        <?php
        }
        ?>
    </div>
</div>


<div id="showTaiTro" style="border: 2px solid gray; text-align: left; color: red; background-color: lavender;
z-index: 100000; display: none; width: 250px; position: fixed; top: 60px; right: 20px">
    <div style="padding: 10px; font-size: smaller">
    MyTree mong có sự ủng hộ của Quý khách để nâng cấp & phát triển dịch vụ. Xin cảm quý khách đã tin tưởng sử dụng MyTree!
    </div>

    <img src="/images/taitro01.png" style="width: 100%" alt="">
</div>

<div class="main_cont">


    <?php

    $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

    $uid = getUserIdCurrent_();

    if (isset($_GET['pid'])) {
        $pid = $_GET['pid'];
//        echo "\nID = $pid";

        $pid = $_GET['pid'];
        $pid0 = qqgetIdFromRand_($pid);

        if (!$objTree = \App\Models\GiaPha::find($pid0)) {
            bl("Không tồn tại cây!", null, 100);
            goto _END;
        }

    } else {
        if ($uid || $domain!='mytree.vn')
            require_once resource_path() . "/views/giapha/list_tree.php";
        else {
            echo "<div data-code-pos='ppp1676969284387' class='container'> <div class='jumbotron text-center ' style='margin-top: 30px'><a style='text-decoration: none' href='/login'><h2>Đăng nhập để tạo cây của bạn</h2></a>";
            echo "</div>
<div style='padding: 30px 10px; border: 1px solid #eee; border-radius: 5px'>
<a style='text-decoration: none' href='/my-tree?pid=js156958'> <h2 style='text-align: center'> Cây Mẫu </h2> <img style='width: 100%' src='/images/mytree/template1.png' alt=''> </a>
</div>
</div>";
        }
    }


    ?>

    <?php
    if (isset($pid)) {
        require_once resource_path() . "/views/giapha/html__tree.php";
    }
    ?>

    <div id="app_gp" style="">
        <div id="check_error_node" style="display: none"></div>
        <div id="info_svg" style="display: none; float: right; color: red"></div>

        <svg id="svg_grid" class="root_svg" xmlns="http://www.w3.org/2000/svg" style="">
            <defs>
                <filter id="whiteOutlineEffect" color-interpolation-filters="sRGB">
                    <feMorphology in="SourceAlpha" result="MORPH" operator="dilate" radius="1"/>
                    <feColorMatrix in="MORPH" result="WHITENED" type="matrix"
                                   values="-1 0 0 0 1, 0 -1 0 0 1, 0 0 -1 0 1, 0 0 0 1 0"/>
                    <feMerge>
                        <feMergeNode in="WHITENED"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
        </svg>

        <div id="debug_svg" style="display: none; font-size: smaller; color: #eee"></div>
    </div>

    <?php
    _END:
    ?>
</div>

<script>
    let disableApiTreeText = 0
</script>

<?php
$params = request()->all();
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($params);
//echo "</pre>";

//Cache::flush();


if(isset($mretAll) && isset($objTree)){

    if(0)
    if(isSupperAdmin_()){
        $mretAll = json_decode(json_encode($mretAll));
        //Tự động sửa lỗi:
        foreach ($mretAll AS &$obj){
            //Tìm xem có là con của vợ lẽ chồng 2 hay không:
            if($obj->child_of_second_married){
                //Nếu có thì xem vợ lẽ này có chồng là cha của obj ko
                //Nếu ko thì xóa mẹ 2 này đi
                foreach ($mretAll AS &$obj1){
                    if($obj1->id == $obj->child_of_second_married){
                        if($obj1->married_with !== $obj->parent_id){
                            echo "<br/>\n Mẹ 2 ko đúngx: $obj->name ($obj1->name , $obj1->married_with !== $obj->parent_id)";
//                            $obj->child_of_second_married = '';
                        }
                    }
                }
            }
        }
    }

    $keyCacheCount = $objMeta->getCacheKeyCountTree($_GET['pid']);
    Cache::put($keyCacheCount, count($mretAll), 30 * 24 * 60 * 60);

?>

<?php

//Tính toán đa cấp:
    if(0)
    if(isSupperAdminDevCookie()){
        echo "<div style='font-size: small; background-color: lavender; padding: 20px'>";
        \App\Models\NetworkMarketing_Meta::tinhProfitNetwork($mretAll);
        echo "<div>";
    }
?>
<script>
    let dataStaticTree = <?php echo json_encode($mretAll) ?>;
<?php

    //Debug only
    if(isSupperAdmin__())
    {
        $mretAll2 = unserialize(serialize($mretAll));
        $mxx = ['title','birthday','image_list','orders','home_address','created_at','updated_at','_image_list','phone_number','email_address','date_of_death','place_heaven','link_remote','set_nu_dinh','col_fix','_public_link',];
        foreach ($mretAll2 AS &$objDb){
            $objDb['id'] = qqgetIdFromRand_($objDb['id']);
            $objDb['parent_id'] = qqgetIdFromRand_($objDb['parent_id']);
            $objDb['married_with'] = qqgetIdFromRand_($objDb['married_with']);
            $objDb['child_of_second_married'] = qqgetIdFromRand_($objDb['child_of_second_married']);
            foreach ($mxx AS $tmp1)
                unset($objDb[$tmp1]);
        }
        echo "// Count = ".count($mretAll) ." // ".json_encode($mretAll2)
    ?>

    <?php
    }
    ?>

    <?php

    if ($objTree->user_id != auth()->id())
        echo 'disableApiTreeText = "Cây Không thuộc Tài khoản của bạn, nên không thể chỉnh sửa!"';
    ?>
</script>
<?php
}
?>

<script>
    <?php



    ?>
    let domainUrl = '<?php
        $domainUrl = \LadLib\Common\UrlHelper1::getUrlWithDomainOnly()
        ?>'
    let tree1 = new clsTreeTopDownCtrl()
    let url
    tree1.apiAdd = domainUrl + '/api/member-tree-mng/add'
    tree1.apiUpdate = domainUrl + '/api/member-tree-mng/update'
    tree1.apiDelete = domainUrl + '/api/member-tree-mng/delete'
    tree1.apiUploadImage = domainUrl + '/api/member-file/upload'
    tree1.apiBearToken = jctool.getCookie('_tglx863516839');

    tree1.isVIP = <?php echo $isVip ?>;


        console.log(" Init tree1 ok ", tree1.apiBearToken);

    <?php



        $setUrl = $domainUrl . "/api/member-tree-mng/tree?pid=0&get_all=1&order_by=orders&order_type=DESC";
        if (isset($params['url1'])) {
            $setUrl = 'https://giapha.galaxycloud.vn/train/_learn_html_css_js/svg%20train/get-data-from-giapha.php';
        } else {
            ?>

    <?php
        }
        if (isset($_GET['pid'])){
        $pid0 = $pid = $_GET['pid'];
        if (isset($_GET['url1'])) {
            $pid = qqgetIdFromRand_($pid);
        }

        if(!in_array($uid, [1,19,9]))
        {

        ?>
        tree1.optEnableMoveBtn = <?php  echo "1" ?>
      <?php

        }
        ?>



        <?php



    {

//        $treeInfo->tree_nodes_xy = cleanInvalidChars($treeInfo->tree_nodes_xy);

        $decode = json_decode($treeInfo->tree_nodes_xy);
        if(!$decode){
//            die("Error: Không thể decode tree_nodes_xy");
            $treeInfo->tree_nodes_xy = null;
        }
    }

        if(isset($params['level']) && $params['level'] && is_numeric($params['level']))
        {
        ?>
        tree1.optMaxRowLevelLimitShow =    <?php echo $params['level'] ?>
        <?php
        }
        ?>

        //111
        tree1.optDisableApiTreeText = disableApiTreeText;
    <?php



        if(isset($treeInfo) && $treeInfo){
        $imgInfo = \App\Components\Helper1::imageShow1($treeInfo, $treeInfo->image_list, 'image_list', 1);
        ?>

        tree1.objBannerTop = <?php
            // Chuyển đổi ID thành string để tránh lỗi precision trong JavaScript
            $treeInfoArray = json_decode(json_encode($treeInfo), true); // Convert to array
            $treeInfoArray['id'] = (string)$treeInfo->id; // Force ID to string
            echo json_encode($treeInfoArray); // Don't use JSON_NUMERIC_CHECK
        ?>;

    if (!tree1.objBannerTop || tree1.objBannerTop.length == 0){
        console.log(" tree1.objBannerTop init..");
        tree1.initTopBannerEmpty();
    }

    tree1.objBannerTop._image_list = '<?php if ($imgInfo) echo $imgInfo[0]->thumb ?>'
    <?php
        if(isDebugIp()) {
//            echo "xxxxxx <pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($treeInfo->toArray());
//            echo "</pre>";
        }
        /**
         *     {{--tree1.objBannerTop.id = '<?php echo $treeInfo->id ?>'--}}
        {{--tree1.objBannerTop.name = '<?php echo $treeInfo->name ?>'--}}
        {{--tree1.objBannerTop.title = '<?php echo $treeInfo->title ?>'--}}
        {{--tree1.objBannerTop.image_list = '<?php if ($imgInfo) echo $treeInfo->image_list ?>'--}}
         */

        if(isSupperAdminDevCookie()){
        ?>
            tree1.optDebugOpt = 33333
    // tree1.optShowDebugIdAndOrders = 1
        <?php
        }
    }

    $params = $_GET;
    if (!isset($pid))
        $pid = 0;
    $setUrl = \LadLib\Common\UrlHelper1::setUrlParam($setUrl, 'pid', $pid);
    $setUrl = \LadLib\Common\UrlHelper1::setUrlParam($setUrl, 'include_brother', 1);
    ?>
    <?php

    if(isset($treeInfo) && $treeInfo instanceof \App\Models\MyTreeInfo);

    if(isset($params['tester'])){
    ?>
    $(function () {
        clsTreeTopDownCtrl.tester1()
    })
    <?php
    }
    else {
    ?>

    $(function () {

        //tree1.widthCell = 81
        tree1.widthCell = <?php echo $treeInfo->getNodeWidth() ?>;
        tree1.heightCell = <?php echo $treeInfo->getNodeHeight() ?>;
        tree1.spaceXBetweenCellDevidedBy = <?php echo $treeInfo->getSpaceNodeX() ?>;;
        tree1.spaceBetweenCellX = tree1.widthCell / tree1.spaceXBetweenCellDevidedBy
        tree1.spaceBetweenCellY =<?php echo $treeInfo->getSpaceNodeY() ?>;
        tree1.idSvgSelector = 'svg_grid'
        tree1.optShowMarried = 1
        tree1.optShowOnlyMan = 0
        tree1.optDisableApiForTestLocalOnly = 0
        tree1.apiIndex = url
        tree1.optFitViewPortToWindow = 1


        <?php

//        if(0)
//        if(isSupperAdmin__())
        {

        ?>
            // tree1.widthCell = 60;
            if(!tree1.objBannerTop.show_node_image) {
                tree1.optRemoveImage = 1
                tree1.heightCell = Math.round(tree1.heightCell * 0.66);
            }

        // tree1.optMaxRowLevelLimitShow = 0
        <?php
        }


        ?>
        console.log(" tree111 = ", tree1);

        if (tree1.optDisableApiForTestLocalOnly) {
            url = "data1.php"
            if (jctool.getUrlParam('url1'))
                url = "data2.php"
        }

        if (dataStaticTree) {
            console.log(" dataStaticTree1 ", typeof dataStaticTree);
            console.log(" dataStaticTree ", dataStaticTree);
            tree1.dataAll = dataStaticTree
            // tree1.dataPart = JSON.parse(JSON.stringify(dataStaticTree))
            // tree1.dataPart = dataStaticTree.slice();
            tree1.dataPart = dataStaticTree

            tree1.setPid = '<?php echo $pid ?>';
            // jQuery('.loader1').show();

            if (tree1.dataPart && tree1.dataPart.length > 0) {
                tree1.drawTreeSvg()
                tree1.setZoomAble()

                // if (tree1.dataPart.length > 10)
                //     tree1.fit()
                console.log(" moveCenterSvgFirstLoad1 ");
                tree1.moveCenterSvgFirstLoad()
            }
            // jQuery('.loader1').hide();
        } else if (0) {
            <?php
            if(0){
            ?>
            jQuery('.loader1').show();
            $.ajax({
                url: tree1.apiIndex,
                type: 'GET',
                beforeSend: function (xhr) {
                    // xhr.setRequestHeader('Authorization', 'Bearer 123456');
                },
                data: {},
                success: function (data, status) {
                    jQuery('.loader1').hide();
                    // console.log(" DataGet = ", data);

                    let dataGet

                    if (data.payload)
                        dataGet = data.payload
                    else {
                        dataGet = JSON.parse(data)
                        if (dataGet.payload)
                            dataGet = dataGet.payload
                    }
                    tree1.dataAll = [...dataGet]
                    tree1.dataPart = dataGet
                    <?php
                        if(isset($_GET['debug1'])){
                        ?>
                        tree1.optShowDebugGrid = 1
                    <?php
                        }
                        if($pid){
                        ?>
                        tree1.setPid = '<?php echo $pid ?>';
                    <?php
                    }
                    ?>
                    tree1.drawTreeSvg()
                    tree1.setZoomAble()
                    // if (tree1.dataPart.length > 10)
                    //     tree1.fit()

                    tree1.moveCenterSvgFirstLoad()

                    // }

                },
                error: function (request, status, error) {
                    jQuery('.loader1').hide();

                    alert("Error: " + error + "\n" + request.responseText);
                    console.log(" Error get api....", error);
                },
            });

            <?php
            }
            ?>
        }
    })
    <?php
    }
    }
    ?>
</script>

<script>

    <?php
    if(0)
    if(getCurrentUserId()  && \LadLib\Common\UrlHelper1::getDomainHostName() == 'mytree.vn'){
    ?>


    $(document).ready(function() {
        // Lấy thời gian cuối cùng mà đoạn mã được thực thi từ localStorage
        var lastExecuted = localStorage.getItem('lastExecutedAds1');

        // Lấy thời gian hiện tại
        var now = new Date().getTime();

        // Kiểm tra xem đã quá 24 giờ kể từ lần cuối cùng đoạn mã được thực thi hay chưa
        if (!lastExecuted || now - lastExecuted > 24 * 60 * 60 * 1000) {
            // Thực thi đoạn mã
            $("#showTaiTro").show();
            setTimeout(function () {
                $("#showTaiTro").hide();
            }, 15000);

            // Cập nhật thời gian cuối cùng mà đoạn mã được thực thi trong localStorage
            localStorage.setItem('lastExecutedAds1', now);
        }
    });

    <?php
    }
    ?>

    $(function (){

        $("#taiTro").on('click', function (){
            $("#showTaiTro").toggle()
        });
        $("#showTaiTro").on('click', function (){
            $("#showTaiTro").hide()
        });
    })

    <?php
    //Đưa đoạn này vào là tải ảnh sẽ lỗi
    //    var app = new Vue({
    //        el: '#app_gp',
    //        data: {
    //        }
    //    })
    ?>
</script>
<form style='display: none; width: 90%; height: 20px' method='post' action='/tool1/mytree/export_excel.php'>
    <textarea name='data' id="data_to_export_excel" style='display: none;'></textarea>
    <button style='display: none;' type='submit' id='export_to_ecxel'> ExportToExcel</button>
    ";
</form>

<?php
if (isset($pid)) {
    require_once resource_path() . "/views/giapha/html__dialog.php";
}



?>

<div class="loader1"></div>

<?php
if(\LadLib\Common\UrlHelper1::getDomainHostName() == 'mytree.vn')
{
?>
<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">
        <span id="fcta-zalo-tracking">Hỗ trợ</span></div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>
<link rel="stylesheet" href="/vendor/zalo.css?x=1">
<?php
}
?>
</body>
</html>



<?php
use Illuminate\Support\Facades\Cache;

try {

$uid = getCurrentUserId();

if(!$uid)
    die("Bạn cần đăng nhập!");

$objCNet = \App\Models\NetworkMarketing::insertOrGetNetworkObj($uid);

//Nếu chưa có pid, thì hỏi xem có set pid hiện tại không

$objLinkP = null;
if ($uidLink = request('idf')) {
    $objLinkP = \App\Models\NetworkMarketing::checkGetValidPid($uidLink);
}


$mmChild = [];

$keyCache = "cache_tree_net_mark_".$uid;
//Cache::forget($keyCache);
if (Cache::has($keyCache)) {
    echo "<!-- GetCache -->";
    $mmChild = Cache::get($keyCache);
}
else
{
    $mmChild = $objCNet->getAllTreeDeep($objCNet->id, null, 1);
    if ($mmChild){
        foreach ($mmChild AS &$objx)
        {
            if(!$objx['name'] || $objx['name'] == 'null')
                $objx['name'] = '';
            if($objx['id'] == $objCNet->id){
                $objx['name'] = 'BẠN';
            }

            $userX = \App\Models\User::find($objx['user_id']);
            $objx['email'] = $userX->email;

            $objx['user_id'] = qqgetRandFromId_($objx['user_id']);

        }
    }

    \Illuminate\Support\Facades\Cache::put($keyCache, $mmChild, 601);
}

//dump($mmChild);
//return;

//function cmp10($a, $b)
//{
//    return $a['level'] > $b['level'];
//}

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    <?php


    $title = 'Liên kết marketing, mạng lưới: ';
    if ($uidLink = request('idf')) {
        $uidLink = qqgetIdFromRand_($uidLink);
        if ($obj = \App\Models\User::find($uidLink)) {
            $title .= ": " . $obj->name;
        }
    }

    echo $title;

    ?>
@endsection
@section("og_title")
    <?php
    echo "$title"
    ?>
@endsection
@section("css")

@endsection

@section("content")

    <?php

        $bonusOne = 10000;

    ?>
    <div class="ladcont" data-code-pos="ppp1682132828815" style="min-height: 500px">
        <div class="container">
            <div class="jumbotron mt-4">
                <?php
                $uidE = qqgetRandFromId_($uid);
                $linkM = \App\Models\NetworkMarketing::getLinkMarketingNetwork($uid);
                $cmail = getCurrentUserEmail();
                echo "- <b> Link marketing của bạn (Mã liên kết:  $uidE | $cmail)</b>  <br>
<input readonly class='form-control mt-1' type='text' style='background-color: snow; width: 100%' value='$linkM'>" . '
<i> Bạn hãy chia sẻ Link này gửi đi cho người dùng khác bạn muốn mời vào hệ thống kinh doanh! </i>';
                if ($uidSetPr = request('set_parent')) {

                    $uidSetPr =  basename(explode('?', $uidSetPr)[0]);
                    if ($objLinkP = \App\Models\NetworkMarketing::checkGetValidPid($uidSetPr)) {

                        try{
                            $objCNet->setBelongUser($uidSetPr);
                        }
                        catch (Throwable $e) { // For PHP 7
                            bl($e->getMessage());
                        }

//                        echo "<br/>\n xxx $uidSetPr";
                    }
//                    die(" DONG Y1");
                }

                if($objPr = $objCNet->getObjParent()){
                    $randid = qqgetRandFromId_($objPr->user_id);
                    $mail2 = \App\Models\User::find($objPr->user_id)->email;
                    echo "<br/>\n- <b>Bạn đã thuộc mạng lưới của </b><b style='color: dodgerblue'>" . $randid . " ($mail2)</b>";
                }
                else{
                    echo "<form method='post'>";
                    ?>
                    {{ csrf_field() }}
                    <?php
                    $randP = null;
                    if($objLinkP)
                        $randP = qqgetRandFromId_($objLinkP->user_id);

                    //Nếu thấy link, nếu link đó là con thì bỏ qua!
//                    if($randP && $randP != $uidE){
//                        // && $objLinkP && $objLinkP->parent_id != $objCNet->id
//                        echo("<br>- Bạn có Muốn đăng ký trọng mạng lưới của <b>" . $randP . "</b> Không ?");
//                        echo "<br/>\n";
//                        echo "<input name='set_parent' type='hidden' value='$randP'> ";
//                        echo "<button type='submit' name='dong_y'> Đồng ý</button>";
//
//                    } else
                    {
                        echo "<br/>\n- <b> Bạn không trong mạng lưới nào! </b>";
                        echo("<br>Bạn có thể nhập Mã Liên kết mà bạn muốn đăng ký vào: ");
                        echo "<br/>\n";
                        echo "<input class='form-control mt-1' name='set_parent' placeholder='Nhập Mã liên kết hoặc link liên kết mà bạn muốn ra nhập' type='text'  value=''> ";
                        echo "<button type='submit' class='btn btn-sm btn-primary mt-2'> Ra nhập </button>";
                    }
                    echo "</form>";
                    echo "<i>Lưu ý: việc ra nhập mạng lưới là không bắt buộc, tuy nhiên khi ra nhập, người tuyến trên có thể hỗ trợ bạn tốt nhất. Ví dụ khi người tuyến trên có số thành viên vượt quá giới hạn, thì thành viên đó sẽ thuộc được đưa xuống tài khoản của bạn... </i>";
                }
                echo "<br/>\n";

                if ($mmChild){

                    echo "<br/>\n - <b> Các thành viên trong cây của bạn: </b>";

                    $mDoanhThu = [];
                    foreach ($mmChild AS &$objx) {
                        if($objx['user_id'] == $uid)
                            continue;

                        if(!$objx['name'] || $objx['name'] == 'null')
                            $objx['name'] = '';

                        if(!isset($mDoanhThu[$objx['level']]))
                            $mDoanhThu[$objx['level']] = 0;
                        if($objx['level'] == 1)
                            $mDoanhThu[$objx['level']] ++;
                        else
                            $mDoanhThu[$objx['level']] ++;
                    }

                    $totalMoney = 0;
                    foreach ($mDoanhThu AS $lv => $count){

                        if($lv == 1){
                            continue;
                            echo "<br/>\n Doanh thu Cấp $lv ($count thành viên): " . ($money = $count * 0);
                        }
                        else{
                            echo "<br/>\n Doanh thu Cấp " . ($lv - 1). " ($count thành viên): " . ($money = $count * $bonusOne)." | " . \LadLib\Common\cstring2::toTienVietNamString3($money);;
                        }
                        $totalMoney += $money;
                    }

                    echo "<br/>\n Tổng doanh thu: <b> $totalMoney | " . \LadLib\Common\cstring2::toTienVietNamString3($totalMoney) . "</b>";
                    echo "<br/>\n";

                    foreach ($mmChild AS &$objx) {
                        $uid1 = $objx['user_id'];
                        if($uid1 == $uid)
                            continue;
//                        if(isset($objx['user_id']))
//                            unset($objx['user_id']);
//                        if(isset($objx['project_id']))
                            unset($objx['project_id']);
                        unset($objx['created_at']);
                        unset($objx['updated_at']);
                        unset($objx['deleted_at']);

//                        if(!$objx['name']);
                       $objx['name'] = $objx['email'];
                        $objx['name'] = str_replace("@", "<br>@", $objx['name']);

                        if(!$objx['parent_id'])
                            $objx['parent_id'] = 0;

                        $rand = qqgetRandFromId_($uid1);
                        $user = \App\Models\User::find($uid1);
                        if (!$user)
                            continue;
                        $lv = ($objx['level']);
//                        echo "<br/>\n Cấp $lv : " . $rand . " - " . $user->name . " - $user->email " ;
                    }
                }

                END1:
                ?>
            </div>


            <div id="app_gp" style="min-height: 500px">
                <div id="check_error_node" style="display: none"></div>
                <div id="info_svg" style="display: none; float: right; color: red"></div>

                <svg id="svg_grid" class="root_svg" xmlns="http://www.w3.org/2000/svg" style="border: 1px dashed #ccc; background-color: snow">
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
        </div>


    </div>




<?php
} catch (Throwable $e) { // For PHP 7
    bl("Error 100: " . $e->getMessage() ." <br> ". $e->getTraceAsString());
    return;
} catch (Exception $e) {
    bl("Error 200: " . $e->getMessage() ." <br> ". $e->getTraceAsString());
    return;
}

//if(request('set_parent'))
//    return;

?>

@endsection


@section('js')
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/bootstrap4/bootstrap.min.css">
{{--    <link rel="stylesheet" href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">--}}

    <link rel="stylesheet" href="/vendor/font-awesome/font-awesome4.css">
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/tool1/lad_tree_vn/clsTreeTopDown.css">

    <script src="/vendor/jquery/jquery-3.6.0.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/vendor/popper.min.js"></script>
    <script src="/vendor/bootstrap4/bootstrap.min.js"></script>
    <script src="/assert/library_ex/js/domti.js"></script>
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


    <?php
    if(isSupperAdmin_()){
    ?>
    <script src="/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js?v=<?php echo filemtime(public_path() . "/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js") ?>"></script>
    <?php
    }else{
    ?>
    <script src="/tool1/lad_tree_vn/tree_glx01.js?v=<?php echo filemtime(public_path() . "/tool1/lad_tree_vn/tree_glx01.js") ?>"></script>
    <?php
    }
    ?>

    <script>
        let disableApiTreeText = 0
    </script>


    <script>
        let dataStaticTree = [{"id":"xg021852","name":"Abc111","title":null,"birthday":null,"image_list":null,"orders":0,"parent_id":0,"married_with":null,"home_address":null,"created_at":"2023-02-03T23:29:33.000000Z","updated_at":"2023-04-21T13:50:17.000000Z","gender":1,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":0,"col_fix":null,"has_child":1,"_public_link":"\/my-tree?pid=xg021852"},{"id":"jc819567","name":"111","title":null,"birthday":null,"image_list":null,"orders":null,"parent_id":"xg021852","married_with":null,"home_address":null,"created_at":"2023-02-07T02:13:14.000000Z","updated_at":"2023-04-18T11:25:06.000000Z","gender":null,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":1,"col_fix":null,"has_child":1,"_public_link":"\/my-tree?pid=jc819567"},{"id":"cs048505","name":"L\u00ea A","title":null,"birthday":null,"image_list":null,"orders":0,"parent_id":"xg021852","married_with":null,"home_address":null,"created_at":"2023-04-21T13:49:39.000000Z","updated_at":"2023-04-21T13:49:39.000000Z","gender":1,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":0,"col_fix":null,"has_child":0,"_public_link":"\/my-tree?pid=cs048505"},{"id":"ha800861","name":"Nguy\u1ec5n A","title":null,"birthday":null,"image_list":null,"orders":0,"parent_id":"xg021852","married_with":null,"home_address":null,"created_at":"2023-04-21T13:50:12.000000Z","updated_at":"2023-04-21T13:50:12.000000Z","gender":1,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":0,"col_fix":null,"has_child":0,"_public_link":"\/my-tree?pid=ha800861"},{"id":"qc479233","name":"111","title":null,"birthday":null,"image_list":null,"orders":0,"parent_id":"jc819567","married_with":null,"home_address":null,"created_at":"2023-02-07T02:32:03.000000Z","updated_at":"2023-04-14T14:22:19.000000Z","gender":1,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":null,"col_fix":null,"has_child":0,"_public_link":"\/my-tree?pid=qc479233"},{"id":"vk315139","name":"L\u00ea B","title":null,"birthday":null,"image_list":null,"orders":0,"parent_id":"jc819567","married_with":null,"home_address":null,"created_at":"2023-04-21T13:50:00.000000Z","updated_at":"2023-04-21T13:50:00.000000Z","gender":1,"child_of_second_married":null,"_image_list":null,"phone_number":null,"email_address":null,"date_of_death":null,"place_heaven":null,"link_remote":null,"set_nu_dinh":0,"col_fix":null,"has_child":0,"_public_link":"\/my-tree?pid=vk315139"}];
        dataStaticTree = <?php echo json_encode($mmChild)  ?>
    </script>

    <script>
        let domainUrl = ''
        let tree1 = new clsTreeTopDownCtrl()
        let url
        // tree1.apiAdd = domainUrl + '/api/member-tree-mng/add'
        tree1.apiUpdate = ''
        // tree1.apiDelete = domainUrl + '/api/member-tree-mng/delete'
        // tree1.apiUploadImage = domainUrl + '/api/member-file/upload'
        tree1.apiBearToken = jctool.getCookie('_tglx863516839');

        tree1.optEnableMoveBtn = 1
        tree1.optFitWindowId = 'app_gp'

        //111
        tree1.optDisableApiTreeText = disableApiTreeText;
        tree1.objBannerTop = {"id":784,"name":"IN GOD WE TRUST","title":"Tri\u1ec7u ph\u00fa trong m\u1ed9t \u0111\u00eam!","tree_id":197,"status":null,"image_list":"4928","color_name":"#e96016","color_title":"#e02929","fontsize_name":50,"fontsize_title":27,"banner_name_margin_top":70,"banner_name_margin_bottom":0,"banner_title_margin_top":44,"banner_title_margin_bottom":0,"member_background_img":"\/images\/border-frame-img2\/a11.png","member_background_img2":"\/images\/border-frame-img2\/a11.png","banner_width":650,"banner_height":242,"banner_name_bold":"bold","banner_name_italic":null,"banner_title_bold":"bold","banner_title_italic":"italic","banner_title_curver":0,"banner_name_curver":0,"banner_text_shadow_name":"text_shadow1","banner_text_shadow_title":"text_shadow1","banner_margin_top":2,"title_before_or_after_name":1};
        if (!tree1.objBannerTop || tree1.objBannerTop.length == 0)
            tree1.initTopBannerEmpty();

        tree1.objBannerTop._image_list = 'https://cdn-img-8.mytree.vn/test_cloud_file?fid=4928'
        tree1.optDebugOpt = 11122
        // tree1.optShowDebugIdAndOrders = 1

        $(function () {
            tree1.widthCell = 80
            tree1.heightCell = 132
            tree1.spaceBetweenCellX = 20
            tree1.spaceBetweenCellY = 50
            tree1.idSvgSelector = 'svg_grid'
            tree1.optShowMarried = 1
            tree1.optShowOnlyMan = 0
            tree1.optDisableApiForTestLocalOnly = 0
            tree1.apiIndex = url
            tree1.optFitViewPortToWindow = 1
            tree1.optDisableMenuNode = '1';

            // tree1.optMaxRowLevelLimitShow = 0
            // console.log(" URLSET = ", tree1.apiIndex);

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

                tree1.setPid = dataStaticTree[0]['id'];
                // jQuery('.loader1').show();

                if (tree1.dataPart && tree1.dataPart.length > 0) {
                    tree1.drawTreeSvg()

                    tree1.setZoomAble()

                    // if (tree1.dataPart.length > 10)
                    //     tree1.fit()
                    tree1.moveCenterSvgFirstLoad()

                    tree1._panZoomTiger.zoomBy(0.7);
                }
                // jQuery('.loader1').hide();
            } else if (0) {
            }
        })
    </script>

    <script>


        $(function () {


        })


    </script>



@endsection

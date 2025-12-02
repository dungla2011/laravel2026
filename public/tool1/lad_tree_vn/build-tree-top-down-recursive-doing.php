<!--
060123:
- Xong tải xuống đúng zoom user đang thấy
- node ko có con thì đặt sát vào anh em
- OK Các chế độ Chỉ hiển thị MAN, bỏ qua Kết hôn
- OK đã dồn hết khoảng trống bên trái

- OK 8.1.23, Đã hoàn thành thêm xóa sửa với cả API!!!
- OK 9.1.23: đã có thứ tự, lớn hơn cho lên đầu,  kể cả vợ cả vợ 2...
    + Duyệt riêng 1 nhánh
- OK 10.1.23: tester ok, refactory ok
- Create new Tree ok
- 12.1.23 ; Upload ảnh check ok, resize max 500px cho tiết kiệm disk

-->

Test lại cây này:
http://localhost:9081/tool1/lad_tree_vn/build-tree-top-down-recursive-doing.php?url1=1&pid=et044175, yf963204, nq415127
Ham nay can sua: chỉ để hàm này this.moveParentsToCenterOfChildren(), thì hàm này cần sửa moveKhongCoConThiMoveSatAnhEm
<?php

require_once __DIR__.'/../../index.php';
//
//$t1 = microtime(1);
//md5_file("e:/download/laravel_2022_lad-main_3.zip");
//$t2 = microtime(1);
//
//echo "<br/>\n";
//echo "\n DT = ". ($t2 - $t1);
//
//return;

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//
//define("DEF_TOOL_CMS", 1);
////$_SERVER['SERVER_NAME'] = '';
//require_once "/var/www/galaxycloud/application/library/base/tool_glx.php";
//ClassNetwork::forwardToOtherDomain($_SERVER['SERVER_NAME']);
//require_once "/var/www/galaxycloud/index.php";;
//$db = MysqliDb::getInstance();

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">
    <link rel="stylesheet" href="/tool1/lad_tree_vn/clsTreeTopDown.css">

    <script src="/vendor/jquery/jquery-3.6.0.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/vendor/popper.min.js"></script>
    <script src="/vendor/bootstrap4/bootstrap.min.js"></script>
    <script src="/assert/library_ex/js/dom-to-image.js"></script>
    <script src="/assert/library_ex/js/FileSaver.js"></script>
    <script src="/assert/library_ex/js/svg-pan-zoom.js"></script>
    <script src="/assert/library_ex/js/hammer.js"></script>
    <script src="/vendor/lazysizes.min.js"></script>
    <script src="/vendor/galaxy/lib_base.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/library_ex/js/jquery-image-upload-resizer.js"></script>
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>-->

<!--    <script src="/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001_check1.js"></script>-->
    <script src="/tool1/lad_tree_vn/clsTreeTopDown_src_glx.001.js"></script>
    <script>

        <?php
        if (! isset($pid)) {
            $pid = 0;
        }
$params = $_GET;
$pid = 0;
if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
}
if (isset($_GET['url1'])) {
    $pid = qqgetIdFromRand_($pid);
}

?>
        <?php
if (isset($params['tester'])) {
    ?>
        $(function () {
            clsTreeTopDownCtrl.tester1()
        })
        <?php
} else {
    ?>
        $(function () {
            let domainUrl = '<?php
            $domainUrl = \LadLib\Common\UrlHelper1::getUrlWithDomainOnly()
    ?>'
            let tree1 = new clsTreeTopDownCtrl()
            let url
            //url = 'https://giapha.galaxycloud.vn/train/_learn_html_css_js/svg%20train/get-data-from-giapha.php'
            //url = "https://a1.pm33.net/api/member-tree-mng/tree?pid=0&get_all=1&order_by=orders";
            <?php
            $setUrl = $domainUrl.'/api/member-tree-mng/tree?pid=0&get_all=1&order_by=orders&order_type=DESC';
    if (isset($params['url1'])) {
        $setUrl = 'https://giapha.galaxycloud.vn/train/_learn_html_css_js/svg%20train/get-data-from-giapha.php';
    } else {
        ?>

            tree1.apiAdd = domainUrl + '/api/member-tree-mng/add'
            tree1.apiUpdate = domainUrl + '/api/member-tree-mng/update'
            tree1.apiDelete = domainUrl + '/api/member-tree-mng/delete'
            tree1.apiUploadImage = domainUrl + '/api/member-file/upload'
            tree1.apiBearToken = jctool.getCookie('_tglx863516839');

            console.log("TokenAPI = ", tree1.apiBearToken);

            <?php
    }
    if ($pid) {
        $setUrl = \LadLib\Common\UrlHelper1::setUrlParam($setUrl, 'pid', $pid);
        $setUrl = \LadLib\Common\UrlHelper1::setUrlParam($setUrl, 'include_brother', 1);
    }
    echo "url = '$setUrl'";
    ?>

            tree1.widthCell = 60
            tree1.spaceBetweenCellX = 20
            tree1.spaceBetweenCellY = 50
            tree1.idSvgSelector = 'svg_grid'
            tree1.optShowMarried = 1
            tree1.optShowOnlyMan = 0
            tree1.optDisableApiForTestLocalOnly = 0
            tree1.apiIndex = url
            tree1.optShowDebugIdAndOrders = 1
            tree1.optFitViewPortToWindow = 1


            console.log(" URLSET = ", tree1.apiIndex);

            if (tree1.optDisableApiForTestLocalOnly) {
                url = "data1.php"
                if (jctool.getUrlParam('url1'))
                    url = "data2.php"
            }

            // if(0)
            $.ajax({
                url: tree1.apiIndex,
                type: 'GET',
                beforeSend: function (xhr) {
                    // xhr.setRequestHeader('Authorization', 'Bearer 123456');
                },
                data: {},
                success: function (data, status) {

                    // console.log(" DataGet = ", data);

                    let dataGet

                    if (data.payload)
                        dataGet = data.payload
                    else {
                        dataGet = JSON.parse(data)
                        if (dataGet.payload)
                            dataGet = dataGet.payload
                    }

                    // console.log("dataGet: ", dataGet, " \nStatus: ", status);

                    tree1.dataAll = [...dataGet]
                    tree1.dataPart = dataGet

                    <?php
            if (isset($_GET['debug1'])) {
                ?>
                    tree1.optShowDebugGrid = 1
                    <?php
            }
    if ($pid) {
        ?>
                    tree1.setPid = <?php echo $pid ?>;
                    <?php
    }
    ?>
                    tree1.drawTreeSvg()
                    tree1.setZoomAble()
                    tree1.moveCenterSvgFirstLoad();

                    // tree1.zoomIn(10);
                    // console.log("Pan1 = ......", tree1._panZoomTiger.getPan().x,  tree1._panZoomTiger.getPan().y);
                    // tree1._panZoomTiger.pan({x: 0, y: 0});
                    // zoomNow()
                },
                error: function () {
                    console.log(" Eror....");
                },
            });


            //if(0)
            //$.ajax({
            //    url: url,
            //    type: 'GET',
            //    beforeSend: function (xhr) {
            //        // xhr.setRequestHeader('Authorization', 'Bearer 123456');
            //    },
            //    data: {},
            //    success: function (data, status) {
            //        let dataGet
            //        if (data.payload)
            //            dataGet = data.payload
            //        else
            //            dataGet = JSON.parse(data)
            //
            //        console.log("dataGet: ", dataGet, " \nStatus: ", status);
            //        let tree1 = new clsTreeTopDownCtrl()
            //        tree1.dataAll = [...dataGet]
            //
            //        tree1.dataPart = dataGet
            //        tree1.widthCell = 60
            //        tree1.spaceBetweenCellX = 20
            //        tree1.spaceBetweenCellY = 50
            //        tree1.idSvgSelector = 'svg_grid'
            //        tree1.optShowMarried = 1
            //        tree1.optShowOnlyMan = 0
            //
            //        <?php
            //        if(isset($_GET['debug1'])){
            //?>
            //        tree1.optShowDebugGrid = 1
            //        <?php
            //        }
            //?>
            //        tree1.drawTreeSvg()
            //        tree1.setZoomAble()
            //
            //    },
            //    error: function () {
            //        console.log(" Eror....");
            //    },
            //});
        })
        <?php
}
?>
    </script>
</head>
<body>

<div id="fixed_top_menu"
     style="width: 100%; position: ; height: 33px; left: 1px; top: 1px; background-color: #ccc; padding: 5px">

    <div>

        <a href="<?php echo \LadLib\Common\UrlHelper1::getUrlWithDomainOnly() ?>/admin/tree-mng"> ADMIN</a> |
        <a href="<?php echo \LadLib\Common\UrlHelper1::getUriWithoutParam() ?>"> Tree 1</a> |
        <a href="<?php echo \LadLib\Common\UrlHelper1::getUriWithoutParam() ?>?url1=1"> Tree 2</a> |

        <?php
$link0 = \LadLib\Common\UrlHelper1::getUriWithoutParam();
$linkDebug = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('debug1', 1);
echo "\n <a href='$link0'> HOME</a> | <a href='$linkDebug'>DEBUG</a> | ";
$linkTester = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('tester', 1);
echo "\n <a href='$linkTester'> Tester</a> | ";
?>

        Download AS:
        <button onclick="clsTreeTopDownCtrl.downloadImagePng('svg_grid', 'img1.jpg')"> PNG</button>
<!--            <button onclick="clsTreeTopDownCtrl.downloadImageSvg('svg_grid', 'svg1.svg')"> SVG</button>-->
<!--            <button onclick="clsTreeTopDownCtrl.zoomIn('svg_grid')"> zoomIn</button>-->
<!--            <button onclick="clsTreeTopDownCtrl.center('svg_grid')"> center</button>-->
<!--            <button onclick="clsTreeTopDownCtrl.fit('svg_grid')"> fit</button>-->
<!--            <button onclick="clsTreeTopDownCtrl.resize('svg_grid')"> resize</button>-->
<!--            <button onclick="clsTreeTopDownCtrl.updateBBox('svg_grid')"> updateBBox</button>-->

        Option:
        <button onclick="clsTreeTopDownCtrl.resetDefault('svg_grid')"> Default</button>
        <button onclick="clsTreeTopDownCtrl.setOnlyMan('svg_grid')"> OnlyMan</button>
        <button onclick="clsTreeTopDownCtrl.setDisableMarried('svg_grid')"> IgnoreMarried</button>
        <button onclick="clsTreeTopDownCtrl.setRemoveImage('svg_grid')"> IgnoreImage</button>
        <button onclick="clsTreeTopDownCtrl.setDisableApiForTestLocal('svg_grid')"> DisableApi</button>
        <button onclick="clsTreeTopDownCtrl.selectBackGround('svg_grid',1)"> BG-Man</button>
        <button onclick="clsTreeTopDownCtrl.selectBackGround('svg_grid',2)"> BG-Woman</button>
        <button onclick="clsTreeTopDownCtrl.showDebugIdOrders('svg_grid')"> ShowDebug ID</button>

        <input type="text" id="first_member_name_of_tree" title="Enter first member name of tree"
               placeholder="Enter name" style="width: 100px">
        <button onclick="clsTreeTopDownCtrl.createNewTree('svg_grid')"> CreateNew Tree</button>
        <span id="debug_svg" style="font-size: smaller; color: red"></span>


    </div>
</div>

<?php
if (isset($_GET['debug1'])) {

    ?>
    <br><br><br><br>
    <table class="debug_table" border="0" style="display: none1">
        <?php
        for ($row = 0; $row < 10; $row++) {
            echo "\n<tr style=''>";
            for ($j = 0; $j < 80; $j++) {
                echo "\n<td style='border-left: 1px solid gray; border-bottom: 1px solid gray'> <span style='font-size: x-small; color: red;font-weight: bold' id='span-debug-$row-$j'>.</span>
 <br> <span style='font-size: smaller'> $row-$j </span></td>";
            }
            echo "\n</tr>";
        }
    ?>
    </table>

    <?php
}
?>

<div id="app_gp" style="">
    <div id="check_error_node" style="display: none"></div>
    <div id="info_svg" style="display: none; float: right; color: red"></div>

    <svg id="svg_grid" class="root_svg" xmlns="http://www.w3.org/2000/svg" style="">
    </svg>


</div>

<div style="display: none; margin: 20px ; margin-top : 100px;  width: 90% height: 1000px; border: 0px solid black; ">
    <svg id="svg_grid2" class="root_svg" xmlns="http://www.w3.org/2000/svg" style="">
    </svg>
</div>

<div id="dialog-select-background" title="Chọn khung ảnh cho thành viên">

    <?php
    for ($i = 101; $i < 166; $i++) {
        echo "<div class='img_bg_node_svg' > <img class='lazyload' data-src='/images/border-frame-img/khung-anh-$i.png'><br> Chọn mẫu $i </div>";
    }
?>

</div>

<div id="dialog-node-add" title="Cập nhật">
    <div id="title_dialog_node" style="margin-bottom: 15px"></div>

    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label">Họ Tên</div>
        <div class="col-sm-8">
            <input autocomplete="off" class="form-control" type="text" id="new_name">
        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label">Giới tính</div>
        <div class="col-sm-8">
            <input autocomplete="off" type="radio" id="new_gender1" name="new_gender" data-val="1" value="1">
            <label for="new_gender1">Nam</label>
            <input autocomplete="off" type="radio" id="new_gender2" name="new_gender" data-val="2" value="2">
            <label for="new_gender2">Nữ</label>
        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label" autocomplete="off">Ngày sinh</div>
        <div class="col-sm-8">
            <input autocomplete="off" class="form-control" style="" type="text" id="new_birthday">
        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label" title="Trong một hàng, số thứ tự cao sẽ lên đầu tiên">Số thứ tự <span
                style="border: 1px solid gray; ; padding: 1px 2px; border-radius: 10px; font-size: smaller"> ? </span>
        </div>
        <div class="col-sm-8">
            <input autocomplete="off" class="form-control" style="" type="text" id="new_orders">
        </div>
    </div>
    <div class="row c" style="font-size: smaller">
        <div class="col-sm-4 label">
            <b>
                Ảnh
            </b>
        </div>
        <div class="col-sm-8">
            <input class="form-control" type="file" accept="image/*" capture="camera" id="file_id" hidden>
            <label class="lb_upload" for="file_id">Chọn ảnh</label>
        </div>
    </div>

    <div onclick="openMore()" class="view_more_prop" style=""> + Thuộc tính khác</div>
    <div style="clear: both"></div>

    <div class="" id="open_more" style="display: none; position: relative">
        <div class="row input_node">
            <div class="col-sm-4 label">Test</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="text" id="new_test">
            </div>
        </div>
        <div class="row input_node">
            <div class="col-sm-4 label">Test</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="text" id="new_test1">
            </div>
        </div>
    </div>

    <div class="row input_node" style="font-size: smaller;">
        <div class="col-sm-12">
            <button style="float: left; left: 40px" class="btn btn-default" onclick="closeAddDialog()">Bỏ qua</button>
            <button style="float: right; right: 40px" class="btn btn-info"
                    onclick="clsTreeTopDownCtrl.saveNewInfoNodeUI()">Ghi lại
            </button>
        </div>


    </div>
</div>

<div style="clear:both;"></div>


</div>

<script>
    function openMore() {
        $("#open_more").toggle()
    }
</script>

</body>
</html>

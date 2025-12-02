<?php
//ladDebug::addTime(__FILE__ . " index.blade view ", __LINE__);
?>

<?php
if (!isset($dataApiUrl))
    dd("*** Error: not found dataApiUrl");
if (!isset($mMetaAll))
    dd("*** Error: not found mMetaAll");
if (!isset($objParamEx))
    dd("*** Error: not found objParamEx");
if (!isset($dataView)) {
//                    goto _END;
//                    dd("*** Error: not found dataView");
}
if (!$mMetaAll) {
    echo "<br/>\n Not found MetaAll0";
}


$objMeta = end($mMetaAll);
if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
//$objMeta->requireViewPos1();
$params = request()->toArray();

$objMetaFolder = $objMeta;
//Nếu có parent folder thì:
if ($objMeta::$folderParentClass) {
    $objMetaFolder = $objMeta::$folderParentClass::getMetaObj();
}


$moduleCurrent = \App\Components\Helper1::getModuleCurrentName(request());

$currentRoute = \App\Components\Route2::current();



$module = \App\Components\Helper1::getModuleCurrentName(request());
$linkIndex = '';
if($urlThisIndex = $objMeta->getAdminUrlWeb($module)){
    $linkIndex = "<a href='$urlThisIndex'> " .  $objMeta::$titleMeta .  " </a>  ";
    $titleModule = $objMeta::$titleMeta;
    if(!$titleModule){
        $tmp = str_replace("\\", "/", ($objMeta::$modelClass ?? 'NOT_CLASS_DEFINED'));
        $titleModule = basename($tmp);
    }

    $linkIndex = "<a data-pos='489757593745'  href='$urlThisIndex'> " .  $titleModule .  " </a>
        <i class='fa fa-fw fa-angle-right'> </i>  ";
}
?>


@section("title_nav_bar")

{{--    {{ ($currentRoute->title_force_ ?? $objMeta::$titleMeta) ?: basename(str_replace("\\", '/', request()->route()->getController()::class)) }}--}}

    {!! $linkIndex !!}
@endsection

@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

{{--@extends("layouts.member")--}}

@section("title")
    {{ $currentRoute->title_force_ ?? $objMeta::$titleMeta }} |
    Index <?php
          //echo basename(request()->route()->getController()::class)
          echo basename(str_replace("\\", "/", request()->route()->getController()::class))
          ?>

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path()."/admins/table_mng.css") ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style>
        .ui-dialog {
            z-index: 10000 !important;
        }
        .content-header .breadcrumb {
            line-height: 1;
        }
    </style>

    <!--  extraCssInclude -->
    <?php

    $objMeta->extraCssInclude();

    ?>
    <?php
    //ladDebug::addTime(__FILE__ . " index blade ", __LINE__);
    ?>
@endsection

@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(base_path()."/public/admins/table_mng.js") ?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(base_path()."/public/vendor/div_table2/div_table2.js") ?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script
        src="/vendor/lad_tree/clsTreeJs-v2.js?v=<?php echo filemtime(base_path()."/public/vendor/lad_tree/clsTreeJs-v2.js") ?>"></script>
    <script
        src="{{asset("admins/tree_selector.js")}}?v=<?php echo filemtime(base_path()."/public/admins/tree_selector.js") ?>"></script>

    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>

    <script>
        $(function () {
            $("[name=created_at], [name=modified_at], [name=time], [name=time_expired], [name=time_start],input[data-field=start_time], input[data-field=end_time], input[data-field=done_at]").datetimepicker({
                format: 'Y-m-d H:i:s',
            });
        })


        //CheckInDBDateTime
        <?php

            if(0)
        if ($mMetaAll){
        foreach ($mMetaAll AS $mt){

            if ($mt instanceof \LadLib\Common\Database\MetaOfTableInDb) ;

            if ($mt->data_type_in_db == 'date'){?>
$("[name='<?php echo $mt->field ?>[]']").datetimepicker({
                format: 'd-m-Y',
            });
                <?php
            }

            if ($mt->data_type_in_db == 'datetime' || $mt->data_type_in_db == 'timestamp'){?>
                $("[name='<?php echo $mt->field ?>[]']").datetimepicker({
                format: 'Y-m-d H:i:s',
            });
                <?php
            }
            if ($mt->isDateType($mt->field)){?>
                $("[name='<?php echo $mt->field ?>[]']").datetimepicker({
                format: 'Y-m-d',
            });
                <?php
            }
            if ($mt->isDateTimeType($mt->field)){ ?>
            $("[name='<?php echo $mt->field ?>[]']").datetimepicker({
                format: 'Y-m-d H:i:s',
            });

            <?php
            }
                        }
        }

        ?>
    </script>
    <?php
    //Nếu là module admin, và có user_id, thì sẽ ko show Tree này, vì ko thể show tree của all thành viên
    if (!$objMeta::$allowAdminShowTree && \App\Components\Helper1::isAdminModule(request()) && isset($mMetaAll['user_id'])){

    }
    else
        //Tuy nhiên có tình huống chỉ admin mới có tree, như news, product...
    if (isset($mMetaAll['parent_id']) && $objMeta::$folderParentClass){
        $sKeyFieldPr = $sKeyField = $objMeta->getSearchKeyField('parent_id');
        if (isset($mMetaAll['parent_list']) && $objMeta->getSearchKeyField('parent_list'))
            $sKeyField = $objMeta->getSearchKeyField('parent_list');
        if (isset($mMetaAll['parent_all']) && $objMeta->getSearchKeyField('parent_all'))
            $sKeyField = $objMeta->getSearchKeyField('parent_all');

        ?>

        <?php
        //ladDebug::addTime(__FILE__ . " index blade ", __LINE__);
        ?>

    <script>
        // dùng để browse path tree
        const treeFolderBrowse = new clsTreeJsV2();
        treeFolderBrowse.bind_selector = "#tree_select_browse"
        treeFolderBrowse.radio1 = false;
        treeFolderBrowse.api_data = '<?php echo $objMetaFolder->getApiUrl($moduleCurrent) ?>';
        // treeFolder2.api_suffix_add = 'create';
        treeFolderBrowse.api_suffix_index = 'tree';
        treeFolderBrowse.disable_drag_drop = 1;
        // treeFolder2.api_suffix_rename = 'rename';
        // treeFolder2.api_suffix_delete = 'delete';
        // treeFolder2.api_suffix_move = 'move';
        treeFolderBrowse.hide_root_node = 0;
        treeFolderBrowse.disable_menu = 1
        treeFolderBrowse.showTree();

        $(function () {
            $("#div_tree_select_browse").dialog({
                width: 500,
                height: 'auto',
                autoResize: true,
                resizable: true,
                position: {my: "center top+50", at: "center top+50", of: window},
                autoOpen: false,
                modal: true,
                open: function (event, ui) {
                    // $('.ui-widget-overlay').bind('click', function () {
                    //     $("#common_dialog").dialog('close');
                    //     $("#common_dialog2").dialog('close');
                    // });
                }
            });
            $(document).on('click', "#tree_select_browse .real_node_item .node_name", function () {
                let nodeID = $(this).parents('.real_node_item').attr('data-tree-node-id');
                console.log(" ---- Click Folder ID: " + nodeID);
                if (nodeID == 0)
                    window.location.href = "<?php echo $objMeta->getAdminUrlWeb($moduleCurrent) ?>"
                else {
                    <?php
                        $currentUrl = \LadLib\Common\UrlHelper1::getUrlRequestUri();
                        echo "console.log('.....Current URL: ', '$currentUrl');";
                        if(request('_add_user_to')){
                            ?>
                            window.location.href = "<?php echo $currentUrl . '&' . $sKeyFieldPr ?>=" + nodeID
                            return;
                        <?php
                        }
                        ?>
                    window.location.href = "<?php echo $objMeta->getAdminUrlWeb($moduleCurrent) . '?' . $sKeyFieldPr ?>=" + nodeID
                }
            })
            $(document).on('click', ".node_parent_list_click", function () {
                let nodeID = $(this).parents('.real_node_item').attr('data-tree-node-id');
                console.log(" ---- Click Folder ID List PR: " + nodeID);
                if (nodeID != 0) {
                    window.location.href = "<?php echo $objMeta->getAdminUrlWeb($moduleCurrent) . '?' . $sKeyField ?>=" + nodeID
                }
            })
            $("#close_browse_tree").click(function () {
                $("#div_tree_select_browse").dialog("close");
            })

            $("#select_browse_parent").on('click', function () {
                $("#div_tree_select_browse").dialog('open');
            })

        });
    </script>


    <script>
        const treeFolder2 = new clsTreeJsV2();
        treeFolder2.bind_selector = "#tree_root_move_item"
        treeFolder2.radio1 = true;
        treeFolder2.api_data = '<?php echo $objMetaFolder->getApiUrl($moduleCurrent) ?>';
        treeFolder2.api_suffix_add = 'create';
        treeFolder2.api_suffix_index = 'tree';
        treeFolder2.api_suffix_rename = 'rename';
        treeFolder2.api_suffix_delete = 'delete';
        treeFolder2.api_suffix_move = 'move';
        treeFolder2.hide_root_node = 0;
        treeFolder2.disable_drag_drop = 1;
        treeFolder2.disable_menu = 1;
        treeFolder2.showTree();

        $(function () {
            let user_token = jctool.getCookie('_tglx863516839');


            // $(document).on('click', "#tree_container .real_node_item .node_name", function () {
            //     let nodeID = $(this).parents('.real_node_item').attr('data-tree-node-id');
            //     console.log(" ---- Click Folder ID: " + nodeID);
            //     if (nodeID == 0)
            //         window.location.href = "/member/file"
            //     else
            //         window.location.href = "/member/file/?" + sField + "=" + nodeID
            // })

            $("#div_move_item_to_folder").dialog({
                width: 500,
                position: {my: "center top+50", at: "center top+50", of: window},
                autoOpen: false,
                modal: true,
                open: function (event, ui) {
                    $('.ui-widget-overlay').bind('click', function () {
                        $("#common_dialog").dialog('close');
                        $("#common_dialog2").dialog('close');
                    });
                }
            });


            $("#btn_close_move_tree").click(function () {
                $("#div_move_item_to_folder").dialog("close");
            })

            $("#btn_move_file").click(function () {
                console.log(" Move file ...");
                let idListSelecting = '';
                $("input.select_one_check").each(function () {
                    if (this.checked && $(this).attr("data-id")) {
                        idListSelecting += "," + $(this).attr("data-id")
                    }
                });
                console.log("idListSelecting = " + idListSelecting);
                console.log("ID = " + this.id);
                let elm = $("#tree_root_move_item input.radio_box_node1:checked")[0]
                console.log("ELM checked0: ", elm);
                let nodeId = '';
                if (elm) {
                    console.log(" Found elm ... parent: ", elm);
                    nodeId = $(elm).parent(".real_node_item").attr('data-tree-node-id');
                }
                console.log("Move to Node ID = ", nodeId);

                if (!nodeId && nodeId !== 0) {
                    alert("Need select one!")
                    return;
                }


                let url = '<?php echo $objMeta->getApiUrl($moduleCurrent) ?>' + "/update-multi"
                var jqXHR = $.ajax({
                    //url: this.api_data + "?cmd=rename&id=" + nodeId + '&to_name='+ nodeName,
                    url: url,
                    type: 'POST',
                    async: false,
                    data: {'id_list': idListSelecting, 'move_to_parent_id': nodeId},
                    headers: {
                        'Authorization': 'Bearer ' + user_token
                        // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        console.log(" RET1 = ", result);
                        // return mRet  = result
                        $("#div_move_item_to_folder").dialog("close");
                        showToastInfoTop("DONE move file!")
                    },
                    error: function (result) {
                        console.log(" RET2 = ", result);
                        if(result.responseJSON?.message)
                            alert("Some error1?" +  (result.responseJSON.message))
                        else
                            alert("Some error2?" +  JSON.stringify(result))

                    },
                });

            })

            $("#move_item_multi").click(function () {

                console.log("MOVE...");

                if ($("#div_move_item_to_folder").length == 0) {
                    alert("Can not move, have not dialog move!")
                    return;
                }

                $("#div_move_item_to_folder").dialog("open");

                $("#show_action_multi_item").hide();
            });
        })

    </script>

    <script>
        const treeFolder3 = new clsTreeJsV2();
        treeFolder3.bind_selector = "#tree_root_move_item2"
        treeFolder3.checkbox1 = true;
        treeFolder3.api_data = '<?php echo $objMetaFolder->getApiUrl($moduleCurrent) ?>';
        treeFolder3.api_suffix_add = 'create';
        treeFolder3.api_suffix_index = 'tree';
        treeFolder3.api_suffix_rename = 'rename';
        treeFolder3.api_suffix_delete = 'delete';
        treeFolder3.api_suffix_move = 'move';
        treeFolder3.hide_root_node = 0;
        treeFolder3.disable_drag_drop = 1;
        treeFolder3.disable_menu = 1;
        treeFolder3.showTree();

        $(function () {

            $("#add_extra_parent").click(function () {
                console.log("Add......");
                if ($("#div_move_item_to_folder2").length == 0) {
                    alert("Can not move, have not dialog move!")
                    return;
                }
                $("#div_move_item_to_folder2").dialog("open");
                $("#show_action_multi_item").hide();
            });

            let user_token = jctool.getCookie('_tglx863516839');


            // $(document).on('click', "#tree_container .real_node_item .node_name", function () {
            //     let nodeID = $(this).parents('.real_node_item').attr('data-tree-node-id');
            //     console.log(" ---- Click Folder ID: " + nodeID);
            //     if (nodeID == 0)
            //         window.location.href = "/member/file"
            //     else
            //         window.location.href = "/member/file/?" + sField + "=" + nodeID
            // })

            $("#div_move_item_to_folder2").dialog({
                width: 500,
                position: {my: "center top+50", at: "center top+50", of: window},
                autoOpen: false,
                modal: true,
                open: function (event, ui) {
                    $('.ui-widget-overlay').bind('click', function () {
                        $("#common_dialog").dialog('close');
                        $("#common_dialog2").dialog('close');
                    });
                }
            });

            $("#select_multi_folder2").dialog({
                width: 500,
                position: {my: "center top+50", at: "center top+50", of: window},
                autoOpen: false,
                modal: true,
                open: function (event, ui) {
                    $('.ui-widget-overlay').bind('click', function () {
                        $("#common_dialog").dialog('close');
                        $("#common_dialog2").dialog('close');
                    });
                }
            });


            $("#btn_close_add_parent2").click(function () {
                $("#select_multi_folder2").dialog("close");
            })


            $("#btn_close_move_tree2").click(function () {
                $("#div_move_item_to_folder2").dialog("close");
            })

            $("#btn_move_file2").click(function () {
                console.log(" Add parent.. ...");
                let idListSelecting = '';
                $("input.select_one_check").each(function () {
                    if (this.checked && $(this).attr("data-id")) {
                        idListSelecting += "," + $(this).attr("data-id")
                    }
                });
                console.log("idListSelecting = " + idListSelecting);
                console.log("ID = " + this.id);
                let elmS = $("#tree_root_move_item2 input.check_box_node1:checked")
                console.log("ELM checked0: ", elmS);
                let nodeId = '';
                if (elmS) {
                    for (let elm of elmS) {
                        nodeId += $(elm).parent(".real_node_item").attr('data-tree-node-id') + ',';
                        console.log(" Found elm ... parent: ", elm);
                    }

                }
                console.log("Move to Node ID = ", nodeId);
                if (!nodeId && nodeId !== 0) {
                    alert("Need select one!")
                    return;
                }


                let url = '<?php echo $objMeta->getApiUrl($moduleCurrent) ?>' + "/update-multi"
                var jqXHR = $.ajax({
                    //url: this.api_data + "?cmd=rename&id=" + nodeId + '&to_name='+ nodeName,
                    url: url,
                    type: 'POST',
                    async: false,
                    data: {'id_list': idListSelecting, 'add_parent_extra': nodeId},
                    headers: {
                        'Authorization': 'Bearer ' + user_token
                        // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        console.log(" RET1 = ", result);
                        // return mRet  = result
                        $("#div_move_item_to_folder2").dialog("close");
                        showToastInfoTop("DONE move file!")
                    },
                    error: function (result) {
                        console.log(" RET2 = ", result);
                        if(result.responseJSON?.message)
                            alert("Some error1?" +  (result.responseJSON.message))
                        else
                            alert("Some error2?" +  JSON.stringify(result))

                    },
                });

            })


        })



    </script>


        <?php
    }


    $objMeta->extraJsInclude();

    ?>

    <script>

        $(".edit_date").datetimepicker({
            format:'d/m/Y',
            mask:true,
        });

        $(".edit_date_time").datetimepicker({
            format:'d/m/Y H:i:s',
            mask:true,
        });


        $("#get_id_list").click(function () {
            console.log("get_id_list...");
            let idlist = clsTableMngJs.getSelectingCheckBox();
            console.log("ID List: ", idlist);

            copyToClipboard(idlist);

            showToastInfoTop("Copied ID List to clipboard!")

            return idlist.join(",");
        });


    </script>

    <?php
    //ladDebug::addTime(__FILE__ . " index blade ", __LINE__);
    ?>
@endsection

@section("content")

    <div id="div_move_item_to_folder" style="display: none" title="Chọn thư mục để di chuyển">
        <div id="tree_root_move_item" class="mt-2" data-code-pos='ppp17256885798831'>
        </div>
        <div class="div_common_sub " style="text-align: center">
            <div class="mt-1 pb-2">
                <button class="btn btn-primary btn-sm" id="btn_move_file"> Chuyển đến
                </button>
                <button class="btn btn-default btn-sm ml-2" id="btn_close_move_tree"> Đóng</button>
            </div>
        </div>
    </div>

    <div id="div_move_item_to_folder2" style="display: none" title="Chọn thư mục để thêm">
        <div id="tree_root_move_item2" style="" data-code-pos='ppp17256885842921'>
        </div>
        <div class="div_common_sub" style="text-align: center">
            <div>
                <button class="btn btn-primary btn-sm" id="btn_move_file2"> Thêm
                </button>
                <button class="btn btn-default btn-sm ml-2" id="btn_close_move_tree2"> Đóng</button>
            </div>
        </div>
    </div>

    <div id="div_tree_select_browse" style="display: none" title="Mở cây Danh mục để duyệt">
        <div id="tree_select_browse" style="">
        </div>
        <div class="div_common_sub" style="text-align: center">
            <div>
                <button class="btn btn-info btn-sm" id="close_browse_tree"> Đóng</button>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <div class="content-header">
            <?php
            $objMeta->extraContentIndex1($dataView, $mMetaAll);
            ?>
            <div class="container-fluid" data-code-pos='ppp16869057826701'>
                <?php



                if($objMeta::$folderParentClass) //Nếu có parent thì mới có Breakum này
                if (!$objMeta->ignoreIndexTable){
                    ?>
                <div class="row mb-2" data-code-pos="ppp1676045469457">

                    <div class="col-sm-12 px-2">

                        <div class="bg-white p-2">
                        <div class="breadcrumb float-sm-right">

                                <?php
                                $objMeta->extraContentIndex2($dataView, $mMetaAll);
                                ?>

                                    <?php
                                if (isset($mMetaAll['parent_id']) && $objMeta::$folderParentClass){

//                                $objMeta->getAdminUrlWeb(\App\Components\Helper1::getModuleCurrentName());

                                    ?>
                                <a title="Cây danh mục"
                                   style="font-size: small; border: 1px solid #ccc; padding: 6px 8px; border-radius: 2px"
                                   data-code-pos="ppp1673435190967" href="<?php
                                    if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);
                                    echo $objMeta->getUrlTreeFolder($moduleCurrent);
                                ?>">
                                    <i class="fa fa-tree"></i>
                                    Tree
                                </a>
                                    <?php
                                }
                                    ?>

                        </div>

                        <div class="m-0" style="">

                            <a title="Thư mục Gốc"
                               style="border: 1px solid #ddd; padding: 2px 3px 2px 5px; border-radius: 2px; margin-right: 5px"
                               href="<?php
                                $uri = \LadLib\Common\UrlHelper1::getUriWithoutParam();
                                echo $uri;

                                //Khong can dung cai nay:
                                //echo $objMeta->getAdminUrlWeb(\App\Components\Helper1::getModuleCurrentName(request())) ?>"
                               data-code-pos="ppp1676079166643">
                                <i class="fa fa-home">  </i>
                            </a>


<?php
                            if (!$objMeta::$allowAdminShowTree && \App\Components\Helper1::isAdminModule(request()) && isset($mMetaAll['user_id'])){
                                //Adminh không có nút brow parent
                            }else
                                //Tính huống news, product... thì cần show
                            if (isset($mMetaAll['parent_id']) && $objMeta::$folderParentClass){

                                ?>
                            <button
                                style="border: 1px solid #ddd; padding: 0px 5px; color: dodgerblue; border-radius: 2px"
                                id="select_browse_parent" title="Mở cây Danh mục để duyệt">
{{--                                <img src="/images/icon/icons8-folder-tree-48.png" style="width: 18px" alt="">--}}
                                <i class="fa fa-folder-open"></i>
                            </button>
                                <?php
                            }

                            //Nếu có parent thì mới có Breakum này
                            if ($objMeta::$titleAfterFolderButton) {
                                echo "<div style='display: inline-block' class='ml-1'> ".$objMeta::$titleAfterFolderButton."</div>";
                            }

                                ?>




                            <div style="display: inline-block; margin-left: 5px " data-code-pos='ppp17136543983031'>
                                <?php
                                if (isset($mMetaAll['parent_id']) && $objMeta::$folderParentClass) {
                                    if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) {
                                        $objFolder = $objMeta::$folderParentClass;
                                        $searchKeyParent0 = $searchKeyParent = $objMeta->getSearchKeyField('parent_id');
                                        if ($valPid = request($searchKeyParent)) {
                                            $valPid0 = $valPid;
                                            if (!is_numeric($valPid))
                                                $valPid = qqgetIdFromRand_($valPid);
                                            echo $objMeta->getPathHtml($valPid);
                                        }
                                    }
                                }
                                ?>
                            </div>

                                <?php
                            if (isset($valPid0)){
                                ?>

                            <a data-code-pos="ppp1681466783738" title="Xem xuất bản" target="_blank" href="<?php

                                    //Ko có id thì sẽ là folder?
                                    if(!request('id')){
                                        $fd1 = $objMeta::$folderParentClass;
                                        $mtFolder = $fd1::getMetaObj();
                                        echo $mtFolder->getPublicLink($valPid0);
                                    }
                                    else
                                        echo $objMeta->getPublicLink($valPid0);


                                ?>">
                                <img style="width: 20px" src="/assert/Ionicons/src/share-alt1-dodgerblue.svg" alt="">
                            </a>

                                <?php
                            }
                                if (request('in_trash')) {
                                    echo "\n <span style='color: red; font-style: italic'> <b>[Thùng Rác] </b></span>";
                                }
                                ?>
                        </div>

                        </div>

                    </div><!-- /.col -->
                    <!-- /.col -->

                </div><!-- /.row -->
                    <?php
                }
                ?>


            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">


                <?php
                if ($objMeta->ignoreIndexTable){
                }else{
                    ?>
                @include("parts.index_common")
                    <?php
                }

                _END:

                //echo "<br/>\nEmpty content!";

                ?>


            </div>


        </div>
        @include("parts.debug_info")

    </div>
    <!-- /.content -->

    </div>

@endsection

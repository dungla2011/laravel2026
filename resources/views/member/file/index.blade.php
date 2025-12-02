@extends("layouts.member")

<?php

$skey = \App\Models\FileUpload_Meta::getSearchKeyFromField('parent_id');

if (!isset($dataApiUrl))
    dd("*** Error: not found dataApiUrl");
if (!isset($mMetaAll))
    dd("*** Error: not found mMetaAll");

if (!isset($objParamEx))
    dd("*** Error: not found objParamEx");

if (!isset($dataView)) {
//                    echo "<br/>\n Empty content";
//                    goto _END;
//                    dd("*** Error: not found dataView");
}
$objMeta = end($mMetaAll);
if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
//$objMeta->requireViewPos1();
$params = request()->toArray();

?>


@section("title")

    <?php
    if($pid = request($skey)) {
        if ($fold = \App\Models\FolderFile::where("id", $pid)->first()) {
            echo "Folder: " . $fold->name;
        } else {
            echo "File Manager";
        }
    }
    else{
        ?>
    {{ $objMeta::$titleMeta  }}
    <?php

    }
    ?>

@endsection

@section('title_nav_bar')
    {{ $objMeta::$titleMeta  }}
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/admins/upload_file.css?v=<?php echo filemtime(public_path().'/admins/upload_file.css'); ?>">

    <style>

    .cls_root_tree{
        padding: 0px;
    }

    .table_grid_index{
        /*margin-top: 5px;*/
    }

    .brc_path{
        display: inline-block;
    }

    .one_item_folder {
        width: 300px;
        height: 34px;
        overflow: hidden;
    }

    .brc_link {
        max-width: 300px;
        overflow: hidden;
    }

    div[data-field-filter='parent_id'] {
        display: none;
    }
    </style>

    <?php
        $objMeta->extraCssInclude();
        ?>

@endsection

@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path().'/admins/table_mng.js');?>"></script>
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/vendor/lad_tree/clsTreeJs-v2.js?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v2.js');?>"></script>
    <script src="{{asset("admins/tree_selector.js")}}"></script>
    <script src="/admins/upload_file.js?v=<?php echo filemtime(public_path().'/admins/upload_file.js');?>"></script>


    <script>
        let objUpload = new clsUploadV2()

        objUpload.url_server = '<?php echo \App\Models\SiteMng::getUploadDomainUrl()  ?>/api/member-file/upload';
        objUpload.bind_selector_upload = 'drop-area-upload1';
        // objUpload.bind_selector_result = 'result-area-upload';
        // objUpload.bind_div_upload_status_all = 'div_upload_status_all';

        objUpload.upload_queue = 0;
        objUpload.uploading = 0;
        objUpload.upload_done = 0;
        objUpload.upload_total = 0;
        objUpload.upload_error = 0;
        objUpload.maxFileCC = 2;
        objUpload.bearerToken = jctool.getCookie('_tglx863516839');
        objUpload.maxSizeUpload = <?php echo \App\Models\SiteMng::getMaxSizeUpload()?>;

        let sField = '<?php echo $searchField = \App\Models\FileUpload_Meta::getSearchKeyFromField('parent_id'); ?>';
        if (jctool.getUrlParam(sField))
            objUpload.set_parent_id = jctool.getUrlParam(sField);

        objUpload.mFileUpload = [];

        $(function (){
            objUpload.initUpload()
        })

    </script>

    <script>
        const treeFolder1 = new clsTreeJsV2();
        treeFolder1.bind_selector = "#tree_container"
        if (jctool.getUrlParam('open_all'))
            treeFolder1.opt_open_all_first = 1;
        treeFolder1.api_data = '/api/demo-folder';
        treeFolder1.api_data = '/api/member-folder-file';
        treeFolder1.api_suffix_add = 'create';
        treeFolder1.api_suffix_rename = 'rename';
        treeFolder1.api_suffix_delete = 'delete';
        treeFolder1.api_suffix_move = 'move';
        treeFolder1.hide_root_node = 0;
        treeFolder1.showTree();
    </script>

    <script>


       const treeFolder2 = new clsTreeJsV2();
       treeFolder2.bind_selector = "#tree_root_move_item"
       treeFolder2.radio1 = true;
       treeFolder2.api_data = '/api/member-folder-file';
       treeFolder2.api_suffix_add = 'create';
       treeFolder2.api_suffix_rename = 'rename';
       treeFolder2.api_suffix_delete = 'delete';
       treeFolder2.api_suffix_move = 'move';
       treeFolder2.hide_root_node = 0;
       treeFolder2.showTree();

       $(function () {
           let user_token = jctool.getCookie('_tglx863516839');


           $(document).on('click', "#tree_container .real_node_item .node_name", function () {
               let nodeID = $(this).parents('.real_node_item').attr('data-tree-node-id');
               console.log(" ---- Click Folder ID: " + nodeID);
               if (nodeID == 0)
                   window.location.href = "/member/file"
               else
                   window.location.href = "/member/file/?" + sField + "=" + nodeID
           })

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


               let url = "/api/member-file/update-multi"
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
                       alert("Can not rename1!")
                       console.log(" RET2 = ", result);
                   },
               });

           })

           $("#move_item_multi").click(function () {
               console.log("MOVE...");
               $("#div_move_item_to_folder").dialog("open");
               $("#show_action_multi_item").hide();
           });
       })
    </script>

    <script>
        $(function () {
            const treeFolderBrowse = new clsTreeJsV2();
            treeFolderBrowse.bind_selector = "#tree_browse_file_container"
            treeFolderBrowse.api_data = '/api/member-folder-file';
            treeFolderBrowse.api_suffix_add = 'create';
            treeFolderBrowse.api_suffix_rename = 'rename';
            treeFolderBrowse.api_suffix_move = 'tree-move';
            treeFolderBrowse.api_suffix_delete = 'tree-delete';
            treeFolderBrowse.tmp_cmd_add_result = 0;
            treeFolderBrowse.treeType = "horizontal_folder";
            treeFolderBrowse.currentNodeToPaste = <?php echo $pid  ? $pid : 0 ?>;

            // treeFolderBrowse.hide_root_node = 0;
            // treeFolderBrowse.showTree();

            $("#create_folder2").on('click', function (){

                treeFolderBrowse.openModalOfNode('<?php echo $pid ?? 0 ?>', "create_node", "Tạo mục mới bên trong: .....", "Tạo");

                if(treeFolderBrowse.tmp_cmd_add_result){
                    console.log("Create folder done ...");
                }
                else {
                    // alert("Create folder fail ...");
                }

            })
            document.addEventListener("move_node_success", function(e) {
                console.log(" move_node_success done");
                console.log(e.detail.message); // "Ajax request completed successfully"

                showToastInfoTop("Di chuyển thành công!");

                //Not move:
                if(e.detail.message.payload == -10){
                    console.log("Ignore move to parent!");
                    return;
                }
                let nodeMoved = $(`div[data-tree-node-id='${e.detail.message.payloadEx}']`);
                nodeMoved.remove();

            })
                // Lắng nghe sự kiện
            document.addEventListener("add_node_success", function(e) {
                console.log(" add_node_success done");
                console.log(e.detail.message); // "Ajax request completed successfully"
                let newId = e.detail.message.payload; // Số mới bạn muốn thay thế

                let templateNode = $(`<?php echo \App\Models\FolderFile_Meta::templateNodeFolderFile(1234567890, '1234567890') ?>`);

                // let divClone = $('.real_node_item.one_item_folder').first().clone();
                let divClone = templateNode.clone();

                // Thay thế giá trị của thuộc tính data-tree-node-id
                let oldId = 1234567890;
                divClone.attr('data-tree-node-id', newId);
                // Tìm thẻ a và thay thế giá trị của thuộc tính href
                divClone.find('a').attr('href', function(i, oldHref) {
                    let regex = new RegExp('<?php echo $skey ?>=' + oldId, 'g');
                    return oldHref.replace(regex, '<?php echo $skey ?>=' + newId);
                });

                divClone.find('span.node_name').text(e.detail.message.payloadEx);
                divClone.css('border-color', 'red');
                divClone.css('color', 'red');
                // Thay đổi màu text của divClone thành màu đỏ

                // Thêm divClone vào trước div đầu tiên có cả hai class real_node_item và one_item_folder

                // $('#tree_browse_file_container').prepend(divClone)
                $('#tree_browse_file_container').find('.seperator_line').first().after(divClone);
                // $('.real_node_item.one_item_folder').first().before(divClone);

                //real_node_item  one_item_folder
                // real_node_item  one_item_folder
            });

        })
    </script>

    <script>
        $(function (){

            $("#share_files").on('click', function (){
                let mId = clsTableMngJs.getSelectingCheckBox();
                console.log(" Click Share file ", mId);

            })

        });

    </script>

@endsection
@section("content")

    <div class="content-wrapper" data-code-pos="ppp1682473987780" style="padding-top: 10px">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">

                <?php
                \App\Models\FileUpload_Meta::includeUploadZoneHtmlSample('drop-area-upload1');
                if(!request('in_trash')) {
                ?>



                <div class="p-2 bg-white mt-1 mb-3" data-code-pos='ppp17253118028071'>
                    {{--                        <div id="tree_container"></div>--}}

                    <div class='m-1' data-code-pos='3453452345366' style="border-color: red" >
                        <?php

                        $pid = request($skey) ?? 0;

                        $fold = \App\Models\FolderFile::where("id", $pid)->first();

                        //echo "<a href='/member/file' title='Your root folder' class='fa fa-folder'>  </a>";
                        ?>
                        <div id='tree_browse_file_container' data-tree-bind-selector='#tree_browse_file_container'  class='cls_root_tree'
                             style='max-height: 400px; overflow-y: auto'>

                        <span style='float: right'>

                            <?php
                            if($fold && $fold instanceof \App\Models\FolderFile){

                            ?>

                            <a target="_blank" href='/d/{{$fold->link1}}'>
                            <i title='Get Link Share of this Folder' class='fa fa-share-alt  text-primary'>
                            </i>
                            </a>

                            <?php
                            }
                            ?>

                           <a href='/member/folder-file'>
                            <i title='Folder List' class='fa fa-folder-open ml-2  text-primary'>
                            </i>
                            </a>


                            <i id='create_folder2' title='Add folder' class='fa fa-plus ml-2  text-primary'></i>
                       </span>
                        <?php
                        echo \App\Models\FolderFile_Meta::templateNodeFolderFile(0, '', 1);

                        if($pid){
                            echo " <i class='fa fa-fw fa-angle-right'>  </i> ";
                            if($fold && $fold instanceof \App\Models\FolderFile)
                                    echo $fold->getBreakumPathHtml(0,0, " <i class='fa fa-fw fa-angle-right'>  </i>");

                        }

                        echo "<div class='seperator_line'></div>";

                        $useRand = 0;
                        $meta = new \App\Models\FolderFile_Meta();
                        if($meta->isUseRandId()){
                            $useRand = 1;
                        }

                        $uid = getCurrentUserId();
                        $mm = \App\Models\FolderFile::where("user_id", $uid)->where("parent_id", $pid)->orderBy('name', 'ASC')->get();
                        foreach ($mm AS $obj){
                            $idOK = $obj->id;
                            if(0)
                            if($useRand) {
                                if($obj->ide__)
                                    $idOK = $obj->ide__;
                                else
                                    $idOK = qqgetRandFromId_($obj->id);
                            }
                            echo \App\Models\FolderFile_Meta::templateNodeFolderFile($idOK, $obj->name);
                        }
                        //Root Elm
//                        echo "</div>";
                        ?>
                        </div>

                    </div>

                        <?php
                    }
                    ?>

                    <!-- Modal content -->
                    <div class="modal_dialog_edit_node" style="text-align: center; display: none">
                        <span style="display: block" class="close_btn">&times;</span>
                        <span class="name_desc">Enter new name</span>
                        <br>
                        <br>
                        <input class="new_name" type="text" style="" value="">
                        <br>
                        <input type="submit" value="Enter" class="btn_create" style="">
                    </div>


                </div>
                <div class="" data-code-pos='ppp17134884871621'>
                    <?php

                        if ($folderId = $skey){
                            if($fobj = \App\Models\FolderFile::find($folderId)){
                                if($fobj instanceof \App\Models\FolderFile){

                                }

                            }
                        }

                    ?>

                    @include("parts.index_common")

                </div>
                <?php
                _END:
                ?>
            </div>

            <div id="div_move_item_to_folder" style="display: none" title="Select item">
                <div id="tree_root_move_item" style="" class="mb-2">
                </div>
                <div class="div_common_sub" style="text-align: center">
                    <div>
                        <button class="btn btn-primary btn-sm" id="btn_move_file"> Chuyển đến
                        </button>
                        <button class="btn btn-default btn-sm" id="btn_close_move_tree"> Đóng </button>
                    </div>
                </div>
            </div>

        </div>
        @include("parts.debug_info")

    </div>
    <!-- /.content -->

    </div>

@endsection


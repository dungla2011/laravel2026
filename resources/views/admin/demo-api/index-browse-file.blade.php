@extends("layouts.browse_file")
@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <style>
        .divTable2{
            border: 0px;
        }
        .divTable2Cell{
            display: none
        }
        .divTable2Cell[data-table-field="id"]{
            display: inline-block;
        }
        .divTable2Cell[data-table-field="name"]{
            display: inline-block;
        }
        .input_value_to_post[data-field="name"]{
            pointer-events: none;
        }
        #save-all-data, #add-new-item{
            display: none;
        }

        .filter_3{
            display: none;
        }
        .filter_9{
            flex: 0 0 100%;
            max-width: 98%;
        }

        div.div_filter_item {
            display: none;
        }
        div.div_filter_item[data-field-filter='name'] {
            display: inline-block;
        }

        div.div_filter_item.search_btn {
            display: inline-block;
        }
        /* width */
        ::-webkit-scrollbar {
            width: 6px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #ccc;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }

    </style>

@endsection

@section('js')
    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path().'/admins/table_mng.js');?>"></script>
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>

    <script>

        function sendToTinyEditor(url) {
            console.log(" Call mySubmit, URL = " + url);
            top.tinymce.activeEditor.windowManager.getParams().oninsert(url);
            top.tinymce.activeEditor.windowManager.close();
        }

        $(".divTable2Row").click(function (){
            let iframeFile = parent.document.querySelector('iframe#id-iframe-browser-file');
            let dataField = iframeFile.getAttribute("data-field");
            console.log(" - Datafield iframe = " + dataField);

            let fileLink = $(this).find(".file_link_cloud").attr("data-src");

            //Nếu là lệnh gửi link đến tinymce thì:
            if(iframeFile.getAttribute("data-cmd") == 'call_for_tiny_editor'){
                sendToTinyEditor(fileLink)
                return
            }

            if(!dataField){
                alert("Not fouind data field...!");
                return
            }

            let fileId = $(this).find("[data-field='id']").attr("value");
            let fileName = $(this).find("input[data-field='name']").attr("value");

            console.log("Click to divTable2Row ..." + fileId) ;
            let oldVal = window.top.$("input[data-field='" + dataField +"']").val();
            console.log(" oldVal ..." + oldVal) ;
            let newVal = jctool.addNumberInStringComma(oldVal, fileId)

            let single_value = window.top.$("input[data-field='" + dataField +"']").hasClass("single_value");

            if(single_value){
                newVal = fileId;
            }
            console.log(" newVal ..." + newVal) ;
            window.top.$("input[data-field='"+ dataField +"']").val(newVal);

            console.log("Add more one item to img list from iframe... filelink = " + fileLink );

            if(oldVal != newVal){
                /////
                let oneImg = `<span data-code-pos="ppp1668242238809" class='img_zone' data-img-id='${fileId}' ui-state-default'> ` +
                    `<img src='${fileLink}' alt='' title='${fileName}'> ` +
                    `<span class='one_node_name fa fa-times' title='remove this: ${fileId}' data-id='${fileId}' data-field='${dataField}'>  ` +
                    `</span> </span>`
                if(single_value)
                    window.top.$('.all_node_name[data-field-img="'+ dataField +'"]').html(oneImg)
                else
                    window.top.$('.all_node_name[data-field-img="'+ dataField +'"]').append(oneImg)
                window.top.showToastBottom("Add one done: " + fileName)
            }
            else{
                window.top.showToastBottom("File added: " + fileName)
            }
        })

    </script>
@endsection

@section("content")
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <?php
                if (!isset($dataApiUrl))
                    dd("*** Error: not found dataApiUrl");
                if (!isset($mMetaAll))
                    dd("*** Error: not found mMetaAll");
                if (!isset($objParamEx))
                    dd("*** Error: not found objParamEx");
                if (!isset($dataView)){
//                    goto _END;
//                    dd("*** Error: not found dataView");
                }

                $objMeta = end($mMetaAll);
                if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
                //$objMeta->requireViewPos1();
                $params = request()->toArray();
                ?>

                    <br>
                @include("parts.index_common")
            </div>
        </div>


    <!-- /.content -->
@endsection

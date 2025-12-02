<?php


?>

@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

{{--@extends("layouts.member")--}}

@section("title")

    TimeFrame Demo

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path() . "/admins/table_mng.css") ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style type="text/css" media="print">
        @page {
            size: landscape;
        }

    </style>
    <style>
        #dialog_1 .cont{
            padding: 10px;
        }
        .divTable2Cell input{
            display: none
        }
        .divTable2Cell .value_cell{
            display: block;
            font-size: x-small;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            padding: 2px 5px;
            cursor: pointer;
            background-color: lavender;
        }
        .option_dialog{
            display: none;
        }
    </style>
@endsection


@section("content")

    <style>

    </style>

    <?php
    $objOrg = new \App\Models\HrSampleTimeEvent();

    $meta = \App\Models\HrSampleTimeEvent::getMetaObj();
    if($meta instanceof \App\Models\HrSampleTimeEvent_Meta);

    $mmMeta = \App\Models\HrSampleTimeEvent::getApiMetaArray();

    $html = "";
    $mHtmlSelectOption = [];
    $mKeyAndValOfField = [];
    foreach ($mmMeta AS $field=>$meta1){
        if(!$meta1->isHtmlSelectOption($field))
            continue;
        $field1 = "_$field";
        $mVal =  $meta->$field1(null, null, null);

        if(!isset($mKeyAndValOfField[$field]))
            $mKeyAndValOfField[$field] = [];


        $html .= "<div class='option_dialog $field'>";
        foreach ($mVal AS $key=>$val){
            $mKeyAndValOfField[$field][$key] = $val;
            $html .= " <input class='inp_change' type='radio' name='$field' id='inp_$field"."_"."$key' data-text='$val' value='$key'> <label for='inp_".$field."_"."$key'> $val </label><br>";
        }
        $html.="</div>";
    }

//    dump($mVal);


        $type = request("type");
        $admLink = $objOrg->getLinkAdmIndex();


        $mFieldIndex = ['id', ...$meta->getEditAllowInIndexFieldList(1)];

        $mmUid = array_column(\App\Models\UserGlx::limit(5)->get()->toArray(), 'id');

//        dump($mmUid);
        $mCatId = [10];
    //$mTimeFrame = ['2023-08-01', '2023-08-02', '2023-08-05','2023-08-06',];
        $mTimeFrame = [];
        for($i = 1; $i<= 10; $i++)
            $mTimeFrame[] = sprintf('2023-08-%02d', $i);

        $mField =  \App\Models\HrSampleTimeEvent::getArrayFieldList();
        $mData = [];
        foreach ($mmUid AS $uid){
            foreach ($mTimeFrame AS $timef){
                $ccCat = 0;
                foreach ($mCatId AS $cat){
                    $ccCat++;
                    $catI = 'cat'.$ccCat;
                    if($tmp = \App\Models\HrSampleTimeEvent::where(['user_id'=>$uid, $catI=>$cat, 'time_frame'=>$timef])->first())
                        $mData[] = $tmp;
                    else{
                        //Không save empty nữa, khi nào có sự kiện mới Insert data vào db qua ajax
//                        $tmp = new \App\Models\HrSampleTimeEvent();
//                        $tmp->user_id = $uid;
//                        $tmp->$catI = $cat;
//                        $tmp->time_frame = $timef;
//                        $tmp->save();
//                        $mData[] = $tmp;
                    }

                }
            }
        }
    ?>


    <div class="content-wrapper">
        <a class="float-right" href="<?php echo $admLink ?>" title="View Data in Full List" target="_blank">[A]</a>
        <div class="content time_sheet">
            <div class="container-fluid">

                <div id="dialog2" style="">

                    <div style="padding: 5px 10px; background-color: #eee">
                        CHỌN GIÁ TRỊ
                        <span class="close_dlg" style="float: right; cursor: pointer"> &#9587;</span>
                    </div>
                    <div style="padding: 10px">

                        <?php
                        echo $html;
                        ?>

                        <br>
                        <button class="close_dlg" >Close</button>
                    </div>


                </div>
                <div id="dialog_1" title="Select dialog">
                    <div class="cont">....</div>
                </div>
                <div class="col-md-12" style="padding-top: 20px">
                    DEMO_TIME_FRAME
                    <?php
                    $url = \LadLib\Common\UrlHelper1::getUriWithoutParam();
                    echo "<a href='$url?type=full'> <button> FULL </button> </a> <a href='$url?type=2'> <button> ONE </button> </a> ";
                    ?>

                    <button id="save_all_table"> Save All </button>

                    <br>
                    <br>
                    <?php
                    if($type == 'full'){
                    ?>

                    <div class="divTable2Body">
                        <div class="divTable2Row divTable2Heading1">
                            <div class="divTable2Cell cellHeader">
                                UID
                            </div>
                            <?php
                            foreach ($mTimeFrame AS $timeF){
                            ?>
                            <div class="divTable2Cell cellHeader">
                                <?php
                                echo $timeF
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        <?php


                        $cellNum = 0;

                            foreach ($mmUid as $uid){
                                ?>
                        <div class="divTable2Row">
                            <div class="divTable2Cell txt">
                                <?php
                                echo $uid
                                ?>
                            </div>

                        <?php
                            foreach ($mTimeFrame AS $timeF){
                                $cellNum++;
                                $dataOk = null;
                                if($mData)
                                foreach ($mData AS $datax){
                                    if($datax->user_id == $uid && $datax->time_frame == $timeF){
                                        $dataOk = $datax;
                                        break;
                                    }
                                }
//                                if(!$dataOk)
//                                    continue;
                            ?>
                            <div data-cell="<?php echo $cellNum ?>" class="divTable2Cell data-cell txt" data-id="<?php echo $dataOk->id ?? '' ?>">


                                <?php

                                foreach ($mFieldIndex AS $field){
//                                    if($field == 'id'){
//                                        $valId = $dataOk->$field;
//                                        //echo "<input data-field='id' type='hidden' value='$valId'>";
//                                        continue;
//                                    }
                                ?>

                                <?php

                                $valKey = '';

                                if($dataOk)
                                    $valKey = $dataOk->$field;

                                $valText = '.';
                                if(isset($mKeyAndValOfField[$field]) && isset($mKeyAndValOfField[$field][$valKey]))
                                    $valText = $mKeyAndValOfField[$field][$valKey];

                                echo "<div class='value_cell' data-uid='$uid' data-time-frame='$timeF' data-field='$field' data-key='$valKey'> <span class=''> $valText </span>";
                                //echo  "<input class='inp_val_cell' data-field='$field' type='hidden' value='$valKey'>";
                                echo "</div>" ;

                                ?>
                                <?php
                                //echo "$field / " . $dataOk->$field . "<br>";
                                ?>

                                <?php
                                }
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                            </div>

                        <?php
                        }

                        ?>


                    </div>

                    <?php

                    //Kiểu 2
                    }else{
                    ?>

                    <div class="divTable2Body">
                        <div class="divTable2Row divTable2Heading1">
                            <div class="divTable2Cell cellHeader">
                                UID
                            </div>
                            <?php
                            foreach ($mFieldIndex AS $field){
                            ?>
                            <div class="divTable2Cell cellHeader">
                                <?php
                                echo $field
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                        </div>


                        <?php

                        if(1){
                        foreach ($mTimeFrame AS $timeF){
                        foreach ($mmUid as $uid){
                        $dataOk = null;
                        foreach ($mData AS $datax){
                            if($datax->user_id == $uid && $datax->time_frame == $timeF){
                                $dataOk = $datax;
                                break;
                            }
                        }
                        if(!$dataOk)
                            continue;
                        ?>
                        <div class="divTable2Row">
                            <div class="divTable2Cell txt">
                                <?php
                                echo $uid
                                ?>
                            </div>

                            <?php
                            foreach ($mFieldIndex AS $field){
                            ?>
                            <div class="divTable2Cell txt">
                                <?php
                                echo $dataOk->$field;
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
                        }
                        }
                        }
                        ?>


                    </div>

                    <?php
                    }
                    ?>
                    <?php

                    __END:

                    ?>
                </div>

            </div>
        </div>

    </div>
    <!-- /.content -->
    </div>


@endsection


@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(base_path() . "/public/admins/table_mng.js") ?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(base_path() . "/public/vendor/div_table2/div_table2.js") ?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script
        src="/vendor/lad_tree/clsTreeJs-v2.js?v=<?php echo filemtime(base_path() . "/public/vendor/lad_tree/clsTreeJs-v2.js") ?>"></script>
    <script
        src="{{asset("admins/tree_selector.js")}}?v=<?php echo filemtime(base_path() . "/public/admins/tree_selector.js") ?>"></script>

    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>

    <script>



        $(document).on('click', ".divTable2Cell select" , function (){
            console.log("Click select 1 ...");
        })



    </script>

    <script>

        //divTable2Cell data-cell
        let user_token = jctool.getCookie('_tglx863516839');

        $("#save_all_table").on('click', function () {

            let mPost = [];
            let nItem = 0

            $(".divTable2Cell.data-cell").each(function () {
                $(this).find("input").each(function (){
                    let dtField = $(this).data('field');;
                    console.log(" dtField = " , dtField , $(this).val());
                    mPost.push({name:  dtField + '[]', value: $(this).val() })
                })
            })

            console.log(" Mpost = ", mPost );

            let url = '/api/hr-sample-time-event/update-multi';
            showWaittingIcon()
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                data: mPost,
                success: function (data, status) {
                    hideWaittingIcon()
                    console.log("Data ret: ", data, " \nStatus: ", status);
                    if (data.message && data.message == 'html_ready')
                        if (data.payload) {
                        }
                },
                error: function (data) {
                    hideWaittingIcon()
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + "\n" + data.responseJSON.message)
                    else
                        alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }
            });

        })

    </script>
    <script>
        $( function() {

            // let lastIdCell = null;
            // let lastCellNum = null;

            class clsTmpCell {
                static lastIdCell;
                static lastCellNum;
                static lastUidCell;
                static lastTimeFrameCell;
            }


            $(".option_dialog input.inp_change").on("change", function (){
                let field = $(this).attr("name");
                let val = $(this).val();
                let text = $(this).attr("data-text");

                console.log("Change ... ID = ", clsTmpCell.lastIdCell , field, val, text);

                let mPost = [{name:field,
                    value: val,
                    user_id: clsTmpCell.lastUidCell,
                    time_frame: clsTmpCell.lastTimeFrameCell
                }];

                console.log(" mpost ", mPost);
                //
                // return;

                //Nếu không có lastIdCell và val khác 0, thì thực hiện insert:
                if(!clsTmpCell.lastIdCell && val){

                    let url = '/api/hr-sample-time-event/add';
                    showWaittingIcon()
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: mPost,
                        success: function (data, status) {
                            hideWaittingIcon()
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if (data.message && data.message == 'html_ready')
                                if (data.payload) {
                                }
                        },
                        error: function (data) {
                            hideWaittingIcon()
                            console.log(" DATAx ", data);
                            if (data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                        }
                    });

                }



                $(".divTable2Cell[data-cell='"+ clsTmpCell.lastCellNum + "'] input.inp_val_cell[data-field='"+ field +"']").val(val);
                $(".divTable2Cell[data-cell='"+ clsTmpCell.lastCellNum + "'] .value_cell[data-field='"+ field +"'] span").html(text);


                $("#dialog2").hide();
            })

            $("#dialog2 .close_dlg").on('click', function (){
                $("#dialog2").hide();
            })

//            $(".cellHeader").on('click', function (e){
            $(".value_cell").on('click', function (e){

                console.log("Click ..." , e.pageX , e.pageY);

                clsTmpCell.lastIdCell = $(this).parents(".divTable2Cell").data("id");
                clsTmpCell.lastCellNum = $(this).parents(".divTable2Cell").data("cell");
                clsTmpCell.lastUidCell = $(this).attr("data-uid");
                clsTmpCell.lastTimeFrameCell = $(this).attr("data-time-frame");

                let field = $(this).attr("data-field");
                let dataKey = $(this).attr("data-key");

                console.log(" DTID , Cell = ", clsTmpCell.lastCellNum , clsTmpCell.lastIdCell, field, dataKey , clsTmpCell.lastUidCell , clsTmpCell.lastTimeFrameCell);

                $("#dialog2").show();

                $("#dialog2").css({'z-index': 100000,
                    'top' : "" +  (e.pageY - $(document).scrollTop() - 50) + "px",
                    'left': "" +   e.pageX + "px"
                });


                $(".option_dialog").hide();
                $(".option_dialog." + field).show();

                //khi click mới Đưa input vào, và đưa input id vào parent
                if(!$(this).find("input.inp_val_cell").length)
                    $(this).append("<input class='inp_val_cell' data-field='" + field + "' type='hidden' value='"+dataKey+"'>");
                if(!$(this).parent().find("input[data-field='id']").length)
                    $(this).parent().append("<input data-field='id' type='hidden' value='" + clsTmpCell.lastIdCell + "'>");

            })


        } );

    </script>

@endsection

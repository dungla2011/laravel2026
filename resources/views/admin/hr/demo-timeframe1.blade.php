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
            /*display: none*/
            background-color: white;
        }
        .value_cell .blue {
            color: blue;
        }

        .value_cell.b_red {
            border: 1px solid brown!important;
            color: red!important;
        }

        .value_cell input {
            background-color: white!important;
            font-size: x-small;
            padding: 1px 3px!important;
        }

        .value_cell .gray {
            color: #bbb;
        }

        .option_dialog{
            display: none;
        }
        #dialog2 {
            min-width: 250px; position: fixed; display: none; background-color: white; box-shadow: 0px 0px 4px 4px #ccc; border: 1px solid #eee
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

        $type = clsConfigTimeFrame::$time_frame_type;
        $admLink = $objOrg->getLinkAdmIndex();

        $mFieldIndex = ['id', ...$meta->getEditAllowInIndexFieldList(1)];

        $mmUid = array_column(\App\Models\UserGlx::limit(5)->get()->toArray(), 'id');

//        dump($mmUid);
        $mCatId = [10];
    //$mTimeFrame = ['2023-08-01', '2023-08-02', '2023-08-05','2023-08-06',];
        $mTimeFrame = [];
        for($i = 1; $i<= 5; $i++)
            $mTimeFrame[] = sprintf('2023-08-%02d', $i);

        $mField =  \App\Models\HrSampleTimeEvent::getArrayFieldList();
        $mData = [];
        $mDataUidAndTime = [];
        foreach ($mmUid AS $uid){
            foreach ($mTimeFrame AS $timef){
                $ccCat = 0;
                foreach ($mCatId AS $cat){
                    $ccCat++;
                    $catI = 'cat'.$ccCat;
                    if($tmp = \App\Models\HrSampleTimeEvent::where(['user_id'=>$uid, $catI=>$cat, 'time_frame'=>$timef])->first()){
                        $mData[] = $tmp;
                    }
                    else{
//                        Không save empty nữa, khi nào có sự kiện mới Insert data vào db qua ajax
                        $tmp = new \App\Models\HrSampleTimeEvent();
                        $tmp->user_id = $uid;
                        $tmp->$catI = $cat;
                        $tmp->time_frame = $timef;
                        $tmp->save();
                        $mData[] = $tmp;
                    }

                    $mDataUidAndTime["$uid-$timef"] = $tmp;

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
                                $dataObj = $mDataUidAndTime["$uid-$timeF"] ?? null;
                                if(!$dataObj)
                                    continue;
                            ?>
                            <div class="divTable2Cell data-cell txt" data-id="<?php echo $dataObj->id ?>">

                                <?php
                                foreach ($mFieldIndex AS $field){
                                    if($field == 'id'){

//                                        $valId = $dataObj->$field;
                                        //echo "<input data-field='id' type='hidden' value='$valId'>";
                                        continue;
                                    }else
                                        \App\Models\HrCommon::getHtmlCell($meta, $mKeyAndValOfField, $dataObj, $field);
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
                            <div class="divTable2Cell cellHeader">
                                Time
                            </div>
                            <?php
                            foreach ($mFieldIndex AS $field){
                            ?>
                            <div class="divTable2Cell cellHeader">
                                <?php
                                echo $meta->getDescOfField($field)
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                        </div>


                        <?php


                        foreach ($mTimeFrame AS $timeF){
                        foreach ($mmUid as $uid){
                        $dataObj = null;
                        foreach ($mData AS $datax){
                            if($datax->user_id == $uid && $datax->time_frame == $timeF){
                                $dataObj = $datax;
                                break;
                            }
                        }
                        if(!$dataObj)
                            continue;
                        ?>
                        <div class="divTable2Row">
                            <div class="divTable2Cell txt">
                                <?php
                                echo $uid
                                ?>
                            </div>
                            <div class="divTable2Cell txt">
                                <?php
                                echo $timeF
                                ?>
                            </div>

                            <?php
                            foreach ($mFieldIndex AS $field){
                            ?>
                            <div class="divTable2Cell data-cell txt" data-id="<?php echo $dataObj->id ?>">
                                <?php
                                if($field == 'id')
                                    echo $dataObj->id;
                                else
                                    \App\Models\HrCommon::getHtmlCell($meta, $mKeyAndValOfField, $dataObj, $field);
                                ?>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
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
            let mPost2 = [];

            $(".divTable2Cell.data-cell").each(function () {
                $(this).find("input").each(function (){

                    let dtField = $(this).data('field');;
                    if(dtField!='id' && !$(this).hasClass('changing'))
                        return;

                    console.log(" dtField = " , dtField , $(this).val());
                    mPost.push({name:  dtField + '[]', value: $(this).val() })

                    let idCell =  $(this).parents(".divTable2Cell").data("id");

                    let inArray = 0;
                    for(let obj of mPost2){

                        console.log(" Obj in post: ", obj);

                        if(obj.id == idCell){
                            inArray = 1;
                            obj[dtField] = $(this).val();
                            break;
                        }
                    }
                    if(!inArray){
                        if(dtField == 'id')
                            mPost2.push({id : idCell})
                        else
                            mPost2.push({id : idCell, [dtField]: $(this).val() })
                    }else{

                    }

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
                // data: mPost,
                data: { dataPostV2 : mPost2},
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

            let lastIdNeedChange = null;

            $(".option_dialog input.inp_change").on("change", function (){
                let field = $(this).attr("name");
                let val = $(this).val();
                let text = $(this).attr("data-text");

                console.log("Change ... ID = ", lastIdNeedChange , field, val, text);

                $(".divTable2Cell[data-id='"+ lastIdNeedChange + "'] input.inp_val_cell[data-field='"+ field +"']").val(val);
                $(".divTable2Cell[data-id='"+ lastIdNeedChange + "'] .value_cell[data-field='"+ field +"'] span").html(text);
                if(val != 0){
                    console.log("set blue");
                    $(".divTable2Cell[data-id='"+ lastIdNeedChange + "'] .value_cell[data-field='"+ field +"'] span").prop('class', 'blue');
                }
                else{
                    console.log("set gray");
                    $(".divTable2Cell[data-id='"+ lastIdNeedChange + "'] .value_cell[data-field='"+ field +"'] span").prop('class', 'gray');
                }

                $("#dialog2").hide();
            })

            $("#dialog2 .close_dlg").on('click', function (){
                $("#dialog2").hide();
            })

//            $(".cellHeader").on('click', function (e){
            $(".value_cell").on('click', function (e){

                console.log("Click ..." , e.pageX , e.pageY);
                let dtId = $(this).parents(".divTable2Cell").data("id");
                let field = $(this).data("field");
                let dataKey = $(this).data("key");

                if(!$(this).parent().find("input[data-field='id']").length)
                    $(this).parent().append("<input data-field='id' type='hidden' value='" + dtId + "'>");

                if(!$(this).hasClass('select_ok'))
                    return;

                $(".value_cell.b_red").removeClass("b_red");

                $(this).addClass('b_red');

                lastIdNeedChange = dtId;
                console.log(" DTID = ", dtId, field);

                $("#dialog2").show();

                $("#dialog2").css({'z-index': 100000,
                    'top' : "" +  (e.pageY - $(document).scrollTop() - 50) + "px",
                    'left': "" +  ( e.pageX + 30) + "px"
                });


                $(".option_dialog").hide();
                $(".option_dialog." + field).show();

                //khi click mới Đưa input vào, và đưa input id vào parent
                if(!$(this).find("input.inp_val_cell").length)
                    $(this).append("<input class='inp_val_cell changing' data-field='" + field + "' type='hidden' value='"+dataKey+"'>");


            })

            $("input.inp_val_cell").focus(function (){
                $(this).addClass("changing");
            })

        } );

    </script>

@endsection

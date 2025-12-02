<?php
use LadLib\Common\UrlHelper1;

?>

@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

{{--@extends("layouts.member")--}}

@section("title")

    <?php
    echo clsConfigTimeFrame::$title;
    ?>

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
        #dialog_1 .cont {
            padding: 10px;
        }

        .divTable2Cell input {
            /*display: none*/
            background-color: white;
        }

        .value_cell .blue {
            color: blue;
        }

        .value_cell.select_ok{

        }

        .divTable2Cell {
            font-size: small;
        }

        .value_cell.b_red {
            border: 1px solid brown !important;
            color: red !important;
        }

        .value_cell input {
            background-color: white !important;
            font-size: x-small;
            padding: 1px 3px !important;
        }

        .value_cell .gray {
            color: #bbb;
        }


        .option_dialog {
            display: none;
            font-size: small;
        }
        .option_dialog label{
            display: inline-block;
            min-width: 100px;
            cursor: pointer;
        }

        .option_dialog label:hover{
            color: red;
        }

        .divTable2Cell.sunDay {
            background-color: lavender;
        }

        .divTable2Cell.satDay {
            background-color: lavenderblush;
        }

        .divTable2Cell.toDay {
            background-color: #bbb;
        }
        @media print{
            @page {size: landscape}
            .main-footer{
                display: none;
            }
            .hide_print {
                display: none;
            }
            ::-webkit-input-placeholder { /* WebKit browsers */
                color: transparent!important;;
                text-shadow:none;
            }
            :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
                color: transparent!important;;
                text-shadow:none;
            }
            ::-moz-placeholder { /* Mozilla Firefox 19+ */
                color: transparent!important;;
                text-shadow:none;
            }
            :-ms-input-placeholder { /* Internet Explorer 10+ */
                color: transparent!important;;
                text-shadow:none;
            }

            ::placeholder {
                /* modern browser */
                color: transparent!important;
            }
        }

        .hide1 {
            display: none;
        }
        .div_user_id_info:hover > * {
            display: block;
        }

        #dialog2 {
            min-width: 250px; position: fixed; display: none; background-color: white; box-shadow: 0px 0px 4px 4px #ccc; border: 1px solid #eee;
            max-height: 500px;
            overflow-y: scroll;
        }

        .cellHeader.toDay {
            color: red;
        }

    </style>
@endsection


@section("content")

    <style>

        ::placeholder {
            /* modern browser */
            color: red;
        }
    </style>

    <?php

    //Set Tree ID
    $setTreeId = request('tree_id');

    $uidSet = request('user_id');
    if ($uidSet)
        if (!\App\Models\User::find($uidSet)) {
            bl("Không có User này: " . strip_tags($uidSet));
            return;
        }

    $isMemberModule = 0;
    if (\App\Components\Helper1::isMemberModule()) {
        $isMemberModule = 1;
        $uidSet = getCurrentUserId();
        $setTreeId = 0;
    }


    //$setTreeId = 11;
    $setUidMng = getCurrentUserId();
    //$setUidMng = 10411;



    if (isAdminACP_())
        $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayTreeWithAdminUid($setUidMng);
    else
        $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayUserManageTree();


    $treeInfo = null;
    if ($setTreeId) {
        $treeInfo = \App\Models\HrOrgTree::find($setTreeId);
        if (!$treeInfo) {
            die("Not found org id: $setTreeId");
        }
    }

    $userid = $setUidMng;

    $email = auth()->user()->email ?? null;



    $mTimeFrame = ['2023-08-01', '2023-08-02', '2023-08-05', '2023-08-06',];

    /////////////////////////////////////

    if (clsConfigTimeFrame::$time_frame_range == 'day') {

        //if(isset($_GET['startWeek']))
        {
            $timeStart = $_GET['startWeek'] ?? \LadLib\Common\clsDateTime2::getWeekStartDay(nowy());
            $timeEnd = nowy(strtotime($timeStart) + 6 * _NSECOND_DAY);
        }
        if($_GET['startMonth'] ?? 0){
            $month = $_GET['startMonth'] ?? date("Y-m");
            $timeStart = $month."-01";
            $timeEnd = $month."-".\LadLib\Common\clsDateTime2::getEndDayOfMonth($timeStart);
        }


        $mTimeFrame = \LadLib\Common\clsDateTime2::getArrayDayBetWeenDates($timeStart, $timeEnd);

        $month = substr($timeStart,0,7);

    }

    if (clsConfigTimeFrame::$time_frame_range == 'month') {
        $month = $_GET['startMonth'] ?? date("Y-m");
        $mTimeFrame = [$month];
    }




    ////////////////////////////////////////////////////
    $objOrg = new clsConfigTimeFrame::$class_data;

    $metaObj = clsConfigTimeFrame::$class_data::getMetaObj();

//    bl(clsConfigTimeFrame::$class_data);

    if ($metaObj instanceof \LadLib\Common\Database\MetaOfTableInDb) ;

    $mmMeta = clsConfigTimeFrame::$class_data::getApiMetaArray();
    $api = $metaObj->getApiUrl();
    $html = "";
    $mHtmlSelectOption = [];
    $mKeyAndValOfField = [];
    foreach ($mmMeta AS $field => $meta1) {
        if (!$meta1->isHtmlSelectOption($field))
            continue;

        $field1 = $field;
        if ($field[0] != '_')
            $field1 = "_$field";

        $mVal = $metaObj->$field1(null, null, null);

        if (!isset($mKeyAndValOfField[$field]))
            $mKeyAndValOfField[$field] = [];



        $html .= "<div class='option_dialog $field'>\n<div class='first_elm'></div>\n";
        foreach ($mVal AS $key => $val) {
            $mKeyAndValOfField[$field][$key] = $val;
            $desc = '';
            if($key && isset(\App\Models\HrCommon::getSessionTypeCacheArray()[$key]))
                $desc = \App\Models\HrCommon::getSessionTypeCacheArray()[$key]->desc;
            if($desc)
                $desc = " ($desc) ";
            $html .= " <div> <input data-code-pos='ppp16916697492571' class='inp_change' type='radio' name='$field'
id='inp_$field" . "_" . "$key' data-text='$val' value='$key'>
<label for='inp_" . $field . "_" . "$key'> $val $desc </label></div>";
        }
        $html .= "</div>";
    }

    $mmEnableLoaiCa = [];
    $m2 = \App\Models\HrConfigSessionOrgIdSalary::where('org_id', $setTreeId)->orderByDesc('orders')->get();
    foreach ($m2 AS $o21){
        if(!isset($mmEnableLoaiCa[$o21->job_title_id]))
            $mmEnableLoaiCa[$o21->job_title_id] = [];
        if($o21->status)
            $mmEnableLoaiCa[$o21->job_title_id][] = $o21->session_type_id;
    }

    $type = clsConfigTimeFrame::$time_frame_type;
    $admLink = $objOrg->getLinkAdmIndex();


    $mFieldIndex = ['id', ...$metaObj->getEditAllowInIndexFieldList(1)];



    $mmUid = \App\Models\HrCommon::getUsersIdInOrgId($setTreeId);
    //Lấy ra các UID của all timesheet tháng này:
    $mNhanVienDaInsertVaoTimeSheet = array_column(App\Models\HrSampleTimeEvent::select('user_id')->distinct()
        ->where('cat1', $setTreeId)
        ->where("time_frame", ">=", "$month-01")
        ->where("time_frame", "<=", "$month-31")
        ->get()?->toArray(), 'user_id' );
    $mmUid = array_unique(array_merge($mmUid, $mNhanVienDaInsertVaoTimeSheet ));

    if($uidSet)
        $mmUid = [$uidSet];

    $mmUid = array_filter($mmUid);


    $mField = clsConfigTimeFrame::$class_data::getArrayFieldList();
    $mData = [];
    $mDataUidAndTime = [];
    foreach ($mmUid AS $uid) {
        foreach ($mTimeFrame AS $timef) {
            $ccCat = 0;
            //foreach ($mCatId AS $cat)
            {
                $ccCat++;

                $fieldCat1 = clsConfigTimeFrame::$cat1_field;

                if ($tmp = clsConfigTimeFrame::$class_data::where(['user_id' => $uid, $fieldCat1 => $setTreeId, 'time_frame' => $timef])->first()) {
                    $mData[] = $tmp;
                } else {
                    $tmp = new clsConfigTimeFrame::$class_data;
                    $tmp->user_id = $uid;
                    $tmp->$fieldCat1 = $setTreeId;
                    $tmp->time_frame = $timef;
                    $tmp->save();
                    $mData[] = $tmp;
                }

                if(!isset($mDataUidAndTime[$uid]))
                    $mDataUidAndTime[$uid] = [];
                $mDataUidAndTime[$uid][$timef] = $tmp;

            }
        }
    }






    ?>


    <div class="content-wrapper">

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
                        <button class="close_dlg">Close</button>
                    </div>
                </div>

                <div class="col-md-12 pt-1 hide_print">
                    <a class="float-right" href="<?php echo $admLink ?>" title="View Data in Full List" target="_blank">[A]</a>

                    <?php
                    if (!$isMemberModule) {

                        if (!isset($mUserAdminTree[$setUidMng])) {
                            bl("Bạn không quản lý cây nào!");
                            goto __END;
                        }

                        if ($setTreeId) {
                            $allowOK = \App\Models\HrOrgTree_Meta::FCheckTreeBelongUidMng($setUidMng, $setTreeId);
                            if (!isAdminACP_())
                                if (!$allowOK) {
                                    bl("Cây $setTreeId không thuộc quyền của bạn!");
                                    goto __END;
                                }
                        }

                        \App\Models\HrCommon::getListOrg($mUserAdminTree, $setUidMng);

                        if (!$setTreeId && !$uidSet){
                            bl("Hãy chọn nhánh!!");
                            goto __END;
                        }



                    }

                    $curl = UrlHelper1::getUriWithoutParam();
                    ?>


                    <div class="m-2 text-bold hide_print" data-code-pos='ppp17044172151891'>
                        CHỌN THÁNG

                        <select class="form-control form-control-sm mr-3" style="width: 120px; display: inline" onchange="location = this.value;">
                            <option value=""> - Tháng - </option>
                            <?php
                            $mMonth = [];
                            $cMonth = date("m");
                            if($cMonth == 1){
                                $mMonth[] = (date("Y") - 1) .'-12';
                                $mMonth[] = (date("Y") - 0) .'-01';
                                $mMonth[] = (date("Y") - 0) .'-02';
                            }
                            elseif($cMonth == 12){
                                $mMonth[] = (date("Y")) .'-'. '12';
                                $mMonth[] = (date("Y") + 1) .'-01';
                                $mMonth[] = (date("Y") + 1) .'-02';
                            }
                            else{
                                $mMonth[] = date("Y") .'-'. sprintf("%02d", date("m") - 1);
                                $mMonth[] = date("Y") .'-'. sprintf("%02d", date("m") );
                                $mMonth[] = date("Y") .'-'. sprintf("%02d", date("m") + 1);
                            }


//                            for($i = date("m") - 1; $i <= date("m") + 1; $i++)
                            foreach ($mMonth AS $m)
                            {
//                                $m = date("Y") .'-'. sprintf("%02d", $i);
                                $link =  UrlHelper1::setUrlParamThisUrl("startMonth", $m);
                                $link =  UrlHelper1::setUrlParam($link, "startWeek", null);
                                $startTime = $_GET['startMonth'] ?? 0;
                                $padSl = '';
                                if($startTime && $startTime == $m)
                                    $padSl = "selected";

                                echo "\n<option $padSl value='$link'> Tháng $m</option>";
                            }
                            ?>
                        </select>

                        <?php
                        $mWeek = \LadLib\Common\clsDateTime2::getWeekRangeFromNowEn(-10,5);
                        $startWeek = $_GET['startWeek'] ?? 0;

                        $linkNext = $linkPrev = UrlHelper1::setUrlParam(null, 'startWeek', \LadLib\Common\clsDateTime2::getWeekStartDay());
                        if($startWeek){
                            $nextW = \LadLib\Common\clsDateTime2::getWeekStartDay(strtotime($startWeek) + 7 * _NSECOND_DAY);
                            $preW = \LadLib\Common\clsDateTime2::getWeekStartDay(strtotime($startWeek) - 7 * _NSECOND_DAY);

                            $link1 = UrlHelper1::setUrlParam(null, 'startMonth', null);
                            $linkNext = UrlHelper1::setUrlParam($link1, 'startWeek', $nextW);
                            $linkPrev = UrlHelper1::setUrlParam($link1, 'startWeek', $preW);
                        }

                        if(clsConfigTimeFrame::$time_frame_range != 'month'){
                        ?>
                        <a href="<?php echo $linkPrev ?>">
                        <<
                        </a>
                        <select class="form-control form-control-sm" style="width: 120px; display: inline" onchange="location = this.value;">
                            <option value=""> - Tuần - </option>
                            <?php
                            foreach($mWeek AS $mw1){
                                $startDay = $mw1[0];

                                $padSl = '';
                                if($startWeek && $startWeek == $startDay)
                                    $padSl = "selected";
                                $link =  UrlHelper1::setUrlParamThisUrl("startWeek", $startDay);
                                $link =  UrlHelper1::setUrlParam($link, "startMonth", null);
                                echo "\n<option $padSl value='$link'>Tuần $startDay</option>";
                            }
                            ?>
                        </select>

                        <a href="<?php echo $linkNext ?>">
                            >>
                        </a>

                        <?php
                        }
                        ?>
                    </div>

                    <div class="mb-3">
                        <?php
                        $url = UrlHelper1::getUriWithoutParam();
                        //echo "<a href='$url?type=full'> <button> FULL </button> </a> <a href='$url?type=2'> <button> ONE </button> </a> ";
                        ?>

                        .
                        <button style="float: right" id="save_all_table" class="btn btn-sm btn-warning"> GHI LẠI
                        </button>
                        <button style="float: right" id="salary_table" class="btn btn-sm btn-primary  mx-3"> BẢNG LƯƠNG
                        </button>
                            <br>
                    </div>

                        <div style="clear: both"></div>

                </div>

                <div class="col-md-12 pt-1">
                <?php
                if(!$type || $type == 'full'){
                ?>

{{--                    <input type="checkbox" class="hideNum" data-hide="num1"> Hiện 1--}}
{{--                    <input type="checkbox" class="hideNum" data-hide="num2"> Hiện 2--}}
{{--                    <input type="checkbox" class="hideNum" data-hide="num3"> Hiện 3--}}
{{--                    <input type="checkbox" class="hideNum" data-hide="num4"> Hiện 4--}}


                <div class="divTable2Body mt-3">
                    <div class="divTable2Row divTable2Heading1">
                        <div class="divTable2Cell cellHeader" data-code-pos="ppp1691375724442">
                            STT
                        </div>
                        <div class="divTable2Cell cellHeader" data-code-pos="ppp1691375722306">
                            <input type="checkbox">
                        </div>
                        <div class="divTable2Cell uinfo cellHeader" data-code-pos="ppp1691375720073">
                            Thành viên
                        </div>
                        <?php
                        foreach ($mTimeFrame AS $timeF){
                        $clsDateColor = "";
                        if(\LadLib\Common\clsDateTime2::isSunDay($timeF))
                            $clsDateColor = 'sunDay';
                        if(\LadLib\Common\clsDateTime2::isSatDay($timeF))
                            $clsDateColor = 'satDay';

                        if(($timeF) == nowy())
                            $clsDateColor = 'toDay';

                        ?>
                        <div class="divTable2Cell cellHeader <?php echo $clsDateColor ?>" data-code-pos="ppp1691375727317">
                            <?php
                            echo "<div class=''  style='border-bottom: 1px solid #ccc; display: block'>" . explode("-", $timeF)[2] . "</div> " . explode("-", $timeF)[1];
                            echo "<br/>\n";
                            echo \LadLib\Common\clsDateTime2::getDayOfDateVN($timeF, "T");

                            ?>
                        </div>
                        <?php
                        }
                        ?>

                        <div class="divTable2Cell cellHeader" data-code-pos="ppp1691317678960">
                            Tổng hợp
                        </div>
                    </div>
                    <?php
                    $cc = 0;
                    foreach ($mmUid as $uid){


                    ?>
                    <div class="divTable2Row" data-uid="<?php echo $uid ?>" data-job-title="<?php echo \App\Models\HrEmployee::where('user_id', $uid)->first()?->job_title ?> ">

                        <div class="divTable2Cell txt text-center" data-code-pos="ppp1691317676248">
                            <?php echo ++$cc ?>
                        </div>
                        <div class="divTable2Cell txt" data-code-pos="ppp1691317673002">
                            <input type="checkbox">
                            <br>
                            <i title="Đổi thứ tự lên trên" class="fa fa-caret-up up_item"></i>
                            <br>
                            <i title="Đổi thứ tự xuống dưới" class="fa fa-caret-down down_item"></i>
                            <br>
                            <i title="Đổi thứ tự xuống dưới cùng"
                               class="fa fa-angle-double-down down_bottom"></i>

                        </div>
                        <div class="divTable2Cell txt uinfo" data-code-pos="ppp1691317678960" data-u1="<?php
                        echo $uid;
                        ?>">
                            <?php
                            echo \App\Models\HrCommon::userInfoCol($uid);
                            ?>
                        </div>

                        <?php
                        foreach ($mTimeFrame AS $timeF){
                        $dataObj = $mDataUidAndTime[$uid][$timeF] ?? null;
                        if (!$dataObj)
                            continue;

                        $padColor = '';
                        if(\LadLib\Common\clsDateTime2::isSunDay($timeF))
                            $padColor = "sunDay";
                        if(\LadLib\Common\clsDateTime2::isSatDay($timeF))
                            $padColor = "satDay";
                        if($timeF == nowy())
                            $padColor = "toDay";
                        ?>
                        <div class="divTable2Cell data-cell txt <?php echo $padColor?> " data-id="<?php echo $dataObj->id ?>" data-code-pos="ppp1691317684c">

                            <?php
                            foreach ($mFieldIndex AS $field) {
                                if ($field == 'id') {
//                                        $valId = $dataObj->$field;
//                                        //echo "<input data-field='id' type='hidden' value='$valId'>";
                                    continue;
                                }
                                \App\Models\HrCommon::getHtmlCell($metaObj, $mKeyAndValOfField, $dataObj, $field, $timeF);
                            }
                            ?>
                        </div>



                        <?php
                        }
                        ?>

                        <div class="divTable2Cell txt uinfo" data-code-pos="ppp169yyy8960">
                            <?php
                            $mmTotalAll = $mmTotalAll ?? [];
                            $tmp = clsConfigTimeFrame::$class_meta;
                            if($tmp instanceof \LadLib\Common\Database\MetaOfTableInDb);
                            //$tmp::tinhToan($mDataUidAndTime, $uid, $mmTotalAll);
                            ?>
                        </div>
                    </div>


                    <?php
                    }
                    ?>

                    <div class="divTable2Row">

                        <div class="divTable2Cell txt text-center" data-code-pos="ppp1697676248">

                        </div>
                        <div class="divTable2Cell txt" data-code-pos="ppp1697673002">

                        </div>
                        <div class="divTable2Cell txt uinfo" data-code-pos="ppp1697678960" >
                            <b>
                            Tổng hợp:
                            </b>
                        </div>

                        <?php
                        foreach ($mTimeFrame AS $timeF){
                        ?>
                        <div class="divTable2Cell data-cell txt <?php echo 'padColor'?> " data-id="<?php echo $dataObj->id ?>" data-code-pos="ppp1691317684023">

                            <?php

                            \App\Models\HrCommon::sumDay($setTreeId, $timeF);
                            ?>

                        </div>
                        <?php
                        }
                        ?>

                        <div class="divTable2Cell txt uinfo" data-code-pos="ppp169yyy458960">
                            TTT
                        </div>
                    </div>

                </div>

                <?php
                //Kiểu 2
                }else{
                ?>
                <div class="divTable2Body">
                    <div class="divTable2Row divTable2Heading1">
                        <div class="divTable2Cell cellHeader" data-code-pos="ppp169193232">
                            STT
                        </div>
                        <div class="divTable2Cell cellHeader">
                            UID
                        </div>
                        <div class="divTable2Cell cellHeader" data-code-pos="ppp1691317690466">
                            Time
                        </div>
                        <?php
                        $fieldMetaEx = $metaObj::getMapColFieldEx();

//                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                        print_r($fieldMetaEx);
//                        echo "</pre>";

                        foreach ($mFieldIndex AS $field){
                            $ignore = 0;
                            if($fieldMetaEx){
                                foreach ($fieldMetaEx AS $fieldEx){
                                    $fieldEx = (object) $fieldEx;
                                    if($fieldEx->field == $field && $fieldEx->org_id == $setTreeId)
                                    if(!$fieldEx->status){
                                        $ignore = 1;
                                        break;
                                    }
                                }
                            }
                            if($ignore)
                                continue;

                        ?>
                        <div class="divTable2Cell cellHeader" data-code-pos='ppp16940727008361' title="<?php echo $field ?>">
                            <?php

                            $desc = $metaObj->getDescOfField($field);


                            if($fieldMetaEx){
                                foreach ($fieldMetaEx AS $fieldEx){
                                    $fieldEx = (object) $fieldEx;
                                    if($fieldEx->field == $field && $fieldEx->org_id == $setTreeId){
                                        $desc = $fieldEx->name;
                                        break;
                                    }
                                }
                            }

                            echo $desc;

                            ?>
{{--                            <i class="fa fa-edit"></i>--}}
                        </div>
                        <?php
                        }
                        ?>
                        <div class="divTable2Cell cellHeader">
                            Tổng hợp
                        </div>
                    </div>

                    <?php
                    $cc = 0;
                    foreach ($mTimeFrame AS $timeF){
                    foreach ($mmUid as $uid){
                    $dataObj = null;
                    foreach ($mData AS $datax) {
                        if ($datax->user_id == $uid && $datax->time_frame == $timeF) {
                            $dataObj = $datax;
                            break;
                        }
                    }
                    if (!$dataObj)
                        continue;
                    ?>
                    <div class="divTable2Row">
                        <div class="divTable2Cell txt text-center" data-code-pos="ppp16913176232">
                            <?php
                            echo ++$cc;
                            ?>
                        </div>
                        <div class="divTable2Cell txt" data-code-pos="ppp1691317693953">
                            <?php
                            echo \App\Models\HrCommon::userInfoCol($uid);
                            ?>
                        </div>

                        <div class="divTable2Cell txt" data-code-pos="ppp1691317696189">
                            <?php
                            echo $timeF
                            ?>
                        </div>

                        <?php
                        foreach ($mFieldIndex AS $field){
                            $ignore = 0;
                            if($fieldMetaEx){
                                foreach ($fieldMetaEx AS $fieldEx){
                                    $fieldEx = (object) $fieldEx;
                                    if($fieldEx->field == $field && $fieldEx->org_id == $setTreeId)
                                        if(!$fieldEx->status){
                                            $ignore = 1;
                                            break;
                                        }
                                }
                            }
                            if($ignore)
                                continue;
                        ?>
                        <div data-code-pos="ppp1691317700742" class="divTable2Cell data-cell txt" data-id="<?php echo $dataObj->id ?>">
                            <?php
                            if ($field == 'id')
                                echo $dataObj->id;
                            else
                                \App\Models\HrCommon::getHtmlCell($metaObj, $mKeyAndValOfField, $dataObj, $field, $timeF);
                            ?>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="divTable2Cell " data-code-pos="ppp1691317696189">
                            <?php
                            $tmp = clsConfigTimeFrame::$class_meta;
                            if($tmp instanceof \LadLib\Common\Database\MetaOfTableInDb);
                            $tmp::tinhToan($mDataUidAndTime, $uid, $mmTotalAll);
                            ?>
                        </div>
                    </div>
                    <?php
                    }
                    }
                    ?>
                </div>

                <?php
                }



                __END:
                ?>
                <div data-code-pos='ppp16947779652801'>
                    Tổng hợp:
                </div>
                <?php

                if($mmTotalAll ?? 0){
                    echo "\n<pre>";
                    print_r($mmTotalAll);
                    echo "</pre>";
                }
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


        $(document).on('click', ".divTable2Cell select", function () {
            console.log("Click select 1 ...");
        })


    </script>

    <script>

        //divTable2Cell data-cell
        let user_token = jctool.getCookie('_tglx863516839');

        $("#salary_table").on('click', function () {
            let strUid = '';
            $(".divTable2Row").each(function (){
                let uid = $(this).attr("data-uid");
                if(uid) {
                    strUid += uid + ','
                }
            })
            strUid = jctool.trimLeftRight(strUid, ',');
            console.log("strUid = ", strUid);

            let link = "/admin/hr-salary-month-user/report2?startMonth=" + '<?php echo request('startMonth') ?>' + "&tree_id=" + <?php echo $setTreeId ?>;
            window.open(link, '_blank');

        })

        $("#save_all_table").on('click', function () {

            let mPost = [];
            let mPost2 = [];

            let nItem = 0;

            $(".divTable2Cell.data-cell").each(function () {
                $(this).find("input").each(function () {

                    let dtField = $(this).data('field');
                    ;
                    if (dtField != 'id' && !$(this).hasClass('changing'))
                        return;

                    let idCell =  $(this).parents(".divTable2Cell").data("id");

                    console.log(" -- ID Cell : ", idCell , ", field : ", dtField, ', value: ', $(this).val() );

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

                    console.log(" dtField = ", dtField, $(this).val());
                    mPost.push({name: dtField + '[]', value: $(this).val()})
                })
            })

            console.log(" Mpost = ", mPost);
            console.log(" Mpost2 = ", mPost2);


            let url = '<?php echo (new clsConfigTimeFrame::$class_meta)->getApiUrl(); ?>/update-multi';
            showWaittingIcon()
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
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
        $(function () {

            let loaiCaEnableAllOrg = JSON.parse('<?php echo json_encode($mmEnableLoaiCa); ?>');
            console.log("Loai ca: loaiCaEnableAllOrg ", loaiCaEnableAllOrg, typeof loaiCaEnableAllOrg);


            let lastIdNeedChange = null;
            $(".option_dialog input.inp_change").on("click", function () {
                let field = $(this).attr("name");
                let val = $(this).val();
                let text = $(this).attr("data-text");

                console.log("Change ... ID = ", lastIdNeedChange, field, val, text);

                $(".divTable2Cell[data-id='" + lastIdNeedChange + "'] input.inp_val_cell[data-field='" + field + "']").val(val);
                $(".divTable2Cell[data-id='" + lastIdNeedChange + "'] .value_cell[data-field='" + field + "'] span").html(text);
                if (val != 0) {
                    console.log("set blue");
                    $(".divTable2Cell[data-id='" + lastIdNeedChange + "'] .value_cell[data-field='" + field + "'] span").prop('class', 'blue');
                } else {
                    console.log("set gray");
                    $(".divTable2Cell[data-id='" + lastIdNeedChange + "'] .value_cell[data-field='" + field + "'] span").prop('class', 'gray');
                }

                $("#dialog2").hide();
            })

            $("#dialog2 .close_dlg").on('click', function () {
                $("#dialog2").hide();
            })

//            $(".cellHeader").on('click', function (e){
            $(".value_cell").on('click', function (e) {

                console.log("Click select session ...", e.pageX, e.pageY);
                let dtId = $(this).parents(".divTable2Cell").data("id");
                let field = $(this).data("field");
                let dataKey = $(this).data("key");
                let jobId = $(this).parents(".divTable2Row").data("job-title");

                if (!$(this).parent().find("input[data-field='id']").length)
                    $(this).parent().append("<input data-field='id' type='hidden' value='" + dtId + "'>");

                if (!$(this).hasClass('select_ok'))
                    return;

                $(".value_cell.b_red").removeClass("b_red");

                $(this).addClass('b_red');

                lastIdNeedChange = dtId;
                console.log(" DTID = ", dtId, field);

                $("#dialog2").show();

                $("#dialog2").css({
                    'z-index': 100000,
                    'top': "" + (e.pageY - $(document).scrollTop() - 50) + "px",
                    'left': "" + (e.pageX - $(document).scrollLeft() + 30) + "px"
                });


                $(".option_dialog").hide();
                $(".option_dialog." + field).show();

                //khi click mới Đưa input vào, và đưa input id vào parent
                if (!$(this).find("input.inp_val_cell").length)
                    $(this).append("<input class='inp_val_cell changing' data-field='" + field + "' type='hidden' value='" + dataKey + "'>");




                ////////////////////////////////////////////
                //Ẩn hiện loại ca ơ đây
                console.log(" loaiCaEnableAllOrg[jobId] = ", loaiCaEnableAllOrg,  jobId,  loaiCaEnableAllOrg[parseInt(jobId)]);
                let loaiCaEnableCurrentJobSelect = loaiCaEnableAllOrg[parseInt(jobId)];
                console.log(" loaiCaEnableAllOrg[jobId] = ", loaiCaEnableCurrentJobSelect);
                //option_dialog

                $("#dialog2 div.option_dialog.num4").find("input.inp_change").each(function () {
                    $(this).parent().hide();
                });

                for(let caId of loaiCaEnableCurrentJobSelect){
                    $("#dialog2 div.option_dialog.num4 input.inp_change[value="+ caId +"]").parent().show();
                    $("#dialog2 div.option_dialog.num4 input.inp_change[value="+ caId +"]").parent().insertBefore('div.option_dialog.num4 .first_elm');
                }

                if(0)
                $("#dialog2 div.option_dialog.num4").find("input.inp_change").each(function (){
                    $(this).parent().hide();
                    let sessionTypeId = $(this).val();
                    //console.log("VAL sessionTypeId = ", sessionTypeId);

                    // if(loaiCaEnableCurrentJobSelect.find(sessionTypeId)){
                    //     console.log("OK id: ",  sessionTypeId);
                    // }
                    if(loaiCaEnableCurrentJobSelect.includes(parseInt(sessionTypeId))){
                        console.log("OK id1: ",  sessionTypeId);
                        //dialog2
                        $(this).parent().show();
                        $(this).parent().insertBefore('div.option_dialog.num4 .first_elm');
                    }
                })


            })

            $("input.inp_val_cell").focus(function () {
                $(this).addClass("changing");
            })


            $(".down_item, .up_item, .down_bottom").on("click", function () {

                let down = 0
                let up = 0
                let down_bottom = 0
                if ($(this).hasClass('down_item')) {
                    down = 1
                }
                if ($(this).hasClass('up_item')) {
                    up = 1;
                }

                if ($(this).hasClass('down_bottom')) {
                    down_bottom = 1;
                }

                let uidP = $(this).parents(".divTable2Row").attr("data-uid")

                let startDiv = '';
                let endDiv = '';

                $(".divTable2Row").each(function () {
                    if ($(this).attr("data-uid")) {
                        if (!startDiv)
                            startDiv = $(this).attr("data-uid")
                        endDiv = $(this).attr("data-uid");
                    }
                })

                console.log(" uidP ", uidP);

                let divToMove = $(this).parents(".divTable2Row")

                let divBottom = null;
                if (down_bottom) {
                    divBottom = $(".divTable2Row[data-uid=" + endDiv + "]")
                    divToMove.insertAfter(divBottom)
                }


                if (down) {
                    // if(!down_bottom)
                    //     divToMove.insertAfter(divToMove.next())
                    // if(down_bottom)
                    divToMove.insertAfter(divToMove.next())
                    if (uidP == endDiv)
                        return;
                }
                if (up) {
                    if (uidP == startDiv)
                        return;
                    divToMove.insertBefore(divToMove.prev())
                }

                let idList = '';
                $(".divTable2Row").each(function () {
                    if ($(this).attr("data-uid"))
                        idList += "," + $(this).attr("data-uid")
                })

                idList = idList.replace(/^,+/, '').replace(/$,+/, '');
                console.log(" IDLIST: ", idList);

                let mPost = {idList: idList, tree_id: '<?php echo $setTreeId ?>'}

                let url = '/api/hr-employee/change_order';
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

                        if (down)
                            divToMove.insertBefore(divToMove.prev())
                        if (up)
                            divToMove.insertAfter(divToMove.next())
                    }
                });

            })

        });

    </script>

    <script>

        $(function (){

            $(".hideNum").change(function (){
                let field = $(this).data('hide');
                console.log(" Field ...", field);
                $('div.value_cell[data-field=' + field + ']').toggle();
            })
        })

    </script>

@endsection

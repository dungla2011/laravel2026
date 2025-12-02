<?php


//dump($mUserAdminTree);

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

$mMonthRange = \LadLib\Common\clsDateTime2::getArrayMonthBetWeenDates(time() - 31 * _NSECOND_DAY, time() + 33 * _NSECOND_DAY);

if ($setMonth = request('set_month')) {


    if (!in_array($setMonth, $mMonthRange)) {
        echo "Allow only: <br><pre>";
        print_r($mMonthRange);
        echo "</pre>";
        die("Not allow $setMonth!");
    }

}

?>

@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

{{--@extends("layouts.member")--}}

@section("title")

    Chấm công: <?php echo $treeInfo?->name . " ($setTreeId) " ?>

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

    <style>
        .ui-dialog {
            z-index: 10000 !important;
        }

        .set_month {
            font-size: large;
            border-radius: 5px;
            padding: 1px 5px;
        }

        .divTable2Cell .div_select_all_check, .div_select_one_check {
            padding: 5px;
        }

        .content-wrapper > .content {
            padding: 0px;
        }

        .divTable2Cell select:disabled {
            color: #aaa !important;
        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: landscape;
        }
    </style>
@endsection


@section("content")

    <style>
        .divTable2Cell span {
            font-size: small;
            padding: 2px 5px;
        }

        .divTable2Cell select.lunch {
            color: grey;
        }


        .divTable2Heading1 .divTable2Cell {
            background-color: snow;
        }

        .divTable2Heading1 {
            background-color: #eee;

        }


        .divTable2Cell.total {
            font-size: small;
            padding: 5px;
        }

        .divTable2Cell + .sunDay {
            background-color: lavender;
        }

        .divTable2Cell + .satDay {
            background-color: lavenderblush;
        }

        .sunDay select, .satDay select {
            background-color: transparent;
        }

        .divTable2Cell select {
            /*font-size: x-small!important;*/
            -webkit-appearance: none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
            text-align: center;
        }

        .set_mult_val_div select {
            padding: 5px;
            border-color: #ccc;
        }

        .time_sheet .btn1 {
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .set_date a:not(:last-child) {
            border-right: 1px solid gray;
            padding-right: 3px;
        }
    </style>



    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?php


        function insertHrSalaryMonth($month, $mIdUserAll)
        {
            foreach ($mIdUserAll AS $uid) {
                if (!\App\Models\HrExtraCostEmployee::where(["user_id" => $uid, 'month' => $month])->first()) {
                    $hrTimeS = new \App\Models\HrExtraCostEmployee();
                    $hrTimeS->month = $month;
                    $hrTimeS->user_id = $uid;
                    $hrTimeS->save();
                }
            }
        }

        if (ini_get('max_input_vars') <= 50000)
            bl(" Error: max_input_vars small ? = " . ini_get('max_input_vars'));

        $thisYear = 2023;
        $thisMonth = "05";
        $thisDay = 31;

        $thisYear = date("Y");
        $thisMonth = date("m");
        $thisDay = date("d");
        $tomorowDay = date("d", time() + _NSECOND_DAY);
        if ($tomorowDay == 1)
            $tomorowDay = $thisDay;


        $priceMeal = 25;
        $fixHourMonth = 360;
        $salaryHour = 20;
        $salaryHourOverTime = 25;

        $limit_from_time = request("limit_from_time");
        $limit_to_time = request("limit_to_time");
        if (!$limit_from_time || $limit_from_time < 1 || $limit_from_time > 31)
            $limit_from_time = 1;
        if (!$limit_to_time || $limit_to_time < 1 || $limit_to_time > 31)
            $limit_to_time = 0;

        $limitDayEdit = 0;

        if (!isAdminACP_())
            $limitDayEdit = 1;


        if ($setMonth)
            list($thisYear, $thisMonth) = explode("-", $setMonth);
        else
            $setMonth = "$thisYear-$thisMonth";

        $mSun = \LadLib\Common\clsDateTime2::getSunDayInMonth($thisYear, $thisMonth);
        $mSat = \LadLib\Common\clsDateTime2::getSatDayInMonth($thisYear, $thisMonth);

        $mMonday = \LadLib\Common\clsDateTime2::getMonDayInMonth($thisYear, $thisMonth);

        $timeString = "$thisYear-$thisMonth-$thisDay";
        $lastDateMonth = date("t", strtotime($timeString));
        if ($thisMonth < date("m")) {
            $lastDateMonth = date("t", strtotime("$thisYear-$thisMonth"));
            $thisDay = $lastDateMonth;
        }

        if (!$limit_to_time)
            $limit_to_time = $lastDateMonth;

        $userid = $setUidMng;

        //Liệt kê các member thuộc Tree hiện tại này và insert TimeSheet cho từng user
        //Ngoài ra là các member có thể không còn thuộc tree do được move sang tree khác, thì vẫn phải còn data cũ ngày cũ chấm công trước đó
        $mNhanVienThuocTree = \App\Models\HrEmployee::where(["parent_id" => $setTreeId])->whereRaw("user_id > 0")->orderBy('orders', 'ASC')->get();
        if ($mNhanVienThuocTree instanceof \Illuminate\Database\Eloquent\Collection) ;
        $mIdUser1 = array_map(function ($obj) {
            if (\App\Models\User::find($obj['user_id']))
                return $obj['user_id'];
        }, $mNhanVienThuocTree->toArray());

        //Lấy ra các UID của all timesheet tháng này:
        $mNhanVienDaInsertVaoTimeSheet = App\Models\HrTimeSheet::select('user_id')->distinct()
            ->where('org_id', $setTreeId)
            ->where("time_frame", ">=", "$thisYear-$thisMonth-01")
            ->where("time_frame", "<=", "$thisYear-$thisMonth-31")
            ->get();

        $mIdUser2 = array_map(function ($obj) {
            if (\App\Models\User::find($obj['user_id']))
                return $obj['user_id'];
        }, $mNhanVienDaInsertVaoTimeSheet->toArray());

        //Nhập danh sách user 2 bên lại
        $mIdUserAll = array_unique(array_merge($mIdUser1, $mIdUser2));
        $mIdUserAll = array_filter($mIdUserAll);

        if ($uidSet)
            $mIdUserAll = [$uidSet];

        //        echo "<br/>\n $tomorowDay";

        insertHrSalaryMonth("$thisYear-$thisMonth", $mIdUserAll);

        //Insert Data
        $mTimeSheet = [];
        //        if($uidSet){
        //
        //        }else
        //Nếu có $setTreeId mới thực hiện, nếu ko thì org_id sẽ insert rỗng, có lúc cần org_id rỗng khi user ko thuộc cây nào, kiểu giám đốc...?
        if ($setTreeId)
            //for ($i = 1; $i <= $tomorowDay; $i++) {
            for ($i = 1; $i <= $lastDateMonth; $i++) {
                $date = $thisYear . '-' . $thisMonth . "-" . sprintf("%02d", $i);
                foreach ($mIdUserAll AS $uid) {
                    //Bắt buộc phải có UID
//                if(!$obj->user_id)
//                    continue;
                    if (!$hrTimeS = \App\Models\HrTimeSheet::where(['org_id' => $setTreeId, "user_id" => $uid, 'time_frame' => $date])->first()) {
                        $hrTimeS = new \App\Models\HrTimeSheet();
                        $hrTimeS->time_frame = $date;
                        $hrTimeS->user_id = $uid;

//                    $hrTimeS->n_hour = 12;
                        $hrTimeS->org_id = $setTreeId;
                        $hrTimeS->save();
                        //Reload from db:
                        $hrTimeS = \App\Models\HrTimeSheet::find($hrTimeS->id);

                        $mTimeSheet[] = $hrTimeS->toArray();
                    } else {
                        $mTimeSheet[] = $hrTimeS->toArray();
                    }
                }
            }

        //Nếu chỉ có UID, thì hrTimeS phải lấy lại tất cả các cây orgid
        if ($uidSet) {
            $mTimeSheet = \App\Models\HrTimeSheet::where(["user_id" => $uidSet])->where("time_frame", ">=", "$thisYear-$thisMonth-01")
                ->where("time_frame", "<=", "$thisYear-$thisMonth-31")->get();
            $mOrgId = [];
            foreach ($mTimeSheet AS $ts) {
                if (!in_array($ts->org_id, $mOrgId))
                    $mOrgId[] = $ts->org_id;
            }
            $mIdUserAll = [];
            //Gắn orgId để list từng hàng orgid cho user
            foreach ($mOrgId AS $ogid) {
                if (!$ogid)
                    continue;
                $mIdUserAll[$ogid] = $uidSet;
            }
        }
        ?>
        <div class="content time_sheet" data-code-pos='ppp16950074363431'>
            <div class="container-fluid">
                <div class="col-md-12" style="padding-top: 20px">
                    <?php
                    $email = auth()->user()->email;
                    if (!$isMemberModule) {
                        echo " <div style='margin-bottom: 10px'>  <b> Danh sách Bộ phận thuộc quyền Quản lý của bạn ($email , ID: $userid) </b></div>";
                        //                    if(!isAdminACP_())
                        if (!isset($mUserAdminTree[$setUidMng])) {
                            bl("Bạn không quản lý cây nào!");
                            goto __END;
                        }
                        $mSubTree = $mUserAdminTree[$setUidMng];
                        foreach ($mSubTree AS $treeId) {
                            $tree = \App\Models\HrOrgTree::find($treeId);
                            $link = "?tree_id=$treeId&limit_from_time=01&limit_to_time=05";
                            if (request('tree_id') == $treeId)
                                echo " <a href='$link'> <button data-code-pos='ppp16861145904161' type='button' class='btn btn-sm btn-warning btn1'>$tree->name (Id: $tree->id)  </button> </a>  ";
                            else
                                echo " <a href='$link'> <button data-code-pos='ppp16861145955801' type='button' class='btn btn-sm btn-info btn1'>$tree->name (Id: $tree->id)  </button> </a>  ";
                        }
                        if (!$setTreeId && !$uidSet)
                            goto __END;
                        if ($setTreeId) {
                            $allowOK = \App\Models\HrOrgTree_Meta::FCheckTreeBelongUidMng($setUidMng, $setTreeId);
                            if (!isAdminACP_())
                                if (!$allowOK) {
                                    bl("Cây $setTreeId không thuộc quyền của bạn!");
                                    goto __END;
                                }
                        }
                    }


                    $mTmpCa = \App\Models\HrTimeSheet_Meta::FgetSessionCaArrayTimeSheet();
                    $mTmpHour = \App\Models\HrTimeSheet_Meta::FgetHourArrayTimeSheet();
                    $mTmpMeal = \App\Models\HrTimeSheet_Meta::FgetMealArrayTimeSheet();

//                    $mTmpLate = \App\Models\HrTimeSheet_Meta::FgetTimeLateArraySheet();

                    ?>
                    <div style="margin: 20px 0px; text-align: center; ">
                        <b style="font-size: x-large">CHẤM CÔNG THÁNG <?php echo "$thisMonth-$thisYear" ?></b>
                        <select class="set_month" onchange="window.location.href=this.value" name="" id="">
                            <?php
                            foreach ($mMonthRange AS $monthOK) {
                                $url2 = \LadLib\Common\UrlHelper1::setUrlParamThisUrl("set_month", $monthOK);
                                $sl0 = null;
                                if ($monthOK == $setMonth)
                                    $sl0 = "selected";
                                echo "\n<option $sl0 value='$url2'> $monthOK </option>";
                            }
                            ?>
                        </select>
                        </b>
                        <?php
                        if($setTreeId){
                        ?>
                        <br>
                        &nbsp; <a class="fa fa-link" target="_blank"
                                  href="/admin/hr-salary-month-user/report?org_id=<?php echo $setTreeId ?>&month=<?php echo $setMonth?>">
                            Xuất bảng lương </a>
                        &nbsp; <a class="fa fa-link" target="_blank"
                                  href="/admin/hr-salary-month-user/report-times?org_id=<?php echo $setTreeId ?>&month=<?php echo $setMonth?>">
                            Xuất bảng công </a>
                        <?php
                        }
                        if(!$uidSet){
                        ?>
                        <br>

                        <div class="set_date" style="font-size: small"> Ngày từ:
                            <?php
                            $mMonday[0] = "01";
                            $range = ["01" => "05", "06" => "10", "11" => "15", "16" => "20", '21' => '25', "26" => $lastDateMonth];

                            foreach ($range AS $fromD => $toD) {
                                $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl("limit_from_time", $fromD);
                                $link = \LadLib\Common\UrlHelper1::setUrlParam($link, "limit_to_time", $toD);
                                $st = '';
                                if ($limit_from_time == $fromD)
                                    $st = ";color: red;; font-weight: bold;";
                                echo "\n  <a style='$st;' href='$link'> $fromD-$toD/$thisMonth</a>  ";
                            }
                            ?>
                        </div>

                        <div class="set_mult_val_div" style="display: inline-block; float: left; margin: 10px 0px">
                            <select name="" id="set_multi_n_hour" style="">
                                <?php
                                foreach($mTmpHour AS $i1=>$str){
                                ?>
                                <option value="<?php echo $i1 ?>"><?php echo $str; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <button id="btn_set_multi_n_hour" class="btn btn-sm btn-info"> Đặt Giờ</button>
                            <select name="" id="set_multi_meal">
                                <?php
                                foreach($mTmpMeal AS $i1=>$str){
                                ?>
                                <option value="<?php echo $i1 ?>"><?php echo $str; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <button id="btn_set_multi_meal" class="btn btn-sm btn-info"> Đặt Ăn</button>
                            <select name="" id="set_multi_n_session">
                                <?php
                                foreach($mTmpCa AS $i1=>$str){
                                ?>
                                <option value="<?php echo $i1 ?>"><?php echo $str; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <button id="btn_set_multi_n_session" class="btn btn-sm btn-info"> Đặt Ca</button>
                            <button id="btn_show_check_box" class="btn btn-sm btn-warning"> Mở Check Box</button>
                        </div>
                        <?php
                        }
                        else {
                            $hrUser = \App\Models\HrEmployee::where("user_id", $uidSet)->first();
                            if ($hrUser)
                                echo "<br/>\n Thành viên: <b> $hrUser?->last_name $hrUser?->first_name ($uidSet) </b>";
                        }

                        if(!$uidSet){
                        ?>
                        <button style="float: right" id="save_all_table" class="btn btn-sm btn-warning"> SAVE ALL
                        </button>
                        <?php
                        }
                        ?>


                    </div>


                    <div class="divTable2 divContainer">
                        <div class="divTable2Body">
                            <div class="divTable2Row divTable2Heading1">

                                <div class="divTable2Cell cellHeader">
                                    STT
                                </div>

                                <?php
                                if(!$uidSet){
                                ?>
                                <div class="divTable2Cell text-center div_select_all_check">
                                    <input class="select_all_check select_one_check" type="checkbox"
                                           title="Select All">
                                </div>
                                <?php
                                }
                                ?>
                                <div class="divTable2Cell cellHeader">
                                    Họ Tên
                                </div>

                                {{--                                <div class="divTable2Cell cellHeader">--}}
                                {{--                                    Option--}}
                                {{--                                </div>--}}

                                <?php


                                for($i = $limit_from_time; $i <= $limit_to_time; $i++){
                                $dateInMonth = sprintf("%02d", $i);
                                $thu = \LadLib\Common\clsDateTime2::getDayOfDateVN(strtotime("$thisYear-$thisMonth-$dateInMonth"), "T");


                                $strDate = "$thisYear-$thisMonth-$dateInMonth";
                                $bgToday = null;
                                if ($strDate == nowy()) {
                                    $bgToday = ";background-color: yellow;";
                                }

                                ?>
                                <div style="<?php echo $bgToday ?>" class="divTable2Cell cellHeader <?php
                                if (in_array($i, $mSun))
                                    echo "sunDay";
                                if (in_array($i, $mSat))
                                    echo "satDay";
                                ?>   "><?php

                                    echo " <div data-code-pos='ppp16879369630811' style='$bgToday; border-bottom: 1px solid black;  display: inline-block'> $dateInMonth </div> " .
                                        "<br> $thisMonth <br> <i style='color: gray'> $thu </i> <br> <a target='_blank' href='/admin/hr-cham-cong?tree_id=$setTreeId&date_only=$strDate'> <i style='color: gray' class='fa fa-info-circle'></i> </a> "
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                                <div class="divTable2Cell cellHeader" data-code-pos='ppp1688f63900'>
                                    Tiền Điện
                                </div>
                                <div class="divTable2Cell cellHeader" data-code-pos='ppp1688f63900'>
                                    Tiền Nước
                                </div>
                                <div class="divTable2Cell cellHeader" data-code-pos='ppp1688f63900'>
                                    Đồng Phục
                                </div>


                                <div class="divTable2Cell cellHeader" data-code-pos='ppp16886390040971'>Tổng hợp
                                </div>


                            </div>
                            <?php

                            $totalHourAll = $totalMealAll = 0;

                            $cc = 0;
                            foreach ($mIdUserAll AS $indexOrOrgId=>$cuid){

                            $obj = \App\Models\User::find($cuid);
                            if (!$obj)
                                continue;

                            ?>


                            <div data-code-pos='ppp16861051978811' class="divTable2Row" data-uid="<?php echo $cuid ?>">

                                <div class="divTable2Cell text-center">
                                    <?php
                                    echo ++$cc;
                                    ?>
                                </div>

                                <?php
                                if(!$uidSet){
                                ?>
                                <div class="divTable2Cell div_select_one_check">

                                    <input type="checkbox" class="select_one_check" data-id="">

                                    <br>
                                    <i title="Đổi thứ tự lên trên" class="fa fa-caret-up up_item"></i>
                                    <br>
                                    <i title="Đổi thứ tự xuống dưới" class="fa fa-caret-down down_item"></i>
                                    <br>
                                    <i title="Đổi thứ tự xuống dưới cùng"
                                       class="fa fa-angle-double-down down_bottom"></i>


                                </div>
                                <?php
                                }
                                ?>
                                <div class="divTable2Cell text-left"
                                     style="min-width: 130px; padding: 5px; font-size: small">
                                    <?php
                                    $hrUser = \App\Models\HrEmployee::where("user_id", $cuid)->first();

                                    if($hrUser){
                                    $hrTitle = \App\Models\HrJobTitle::find($hrUser ? $hrUser->job_title : null);
                                    echo " <b> $hrUser->last_name $hrUser->first_name </b> " .
                                        "<br><a target='_blank' href='/admin/hr-employee/edit/$hrUser->id'> Mã Ns: $cuid </a><br> $hrTitle?->name";
                                    //Show tên nhánh:
                                    if ($uidSet) {
                                        if ($orgX = \App\Models\HrOrgTree::find($indexOrOrgId))
                                            echo "<br/>\n <b> Bộ phận: $orgX->name ($indexOrOrgId) </b>";
                                    } else {
                                        echo "<br/>\n";
                                        echo "<a title='xem riêng user này' target='_blank' href='/admin/hr-cham-cong2?user_id=$cuid'> <i class='fa fa-plus'></i></a>";
                                    }
                                    }
                                    ?>
                                </div>


                                <?php


                                $totalHourUser = 0;
                                $totalMealUser = 0;
                                $totalHourUserBonus = 0;
                                $nCa = 0;


                                for($i = $limit_from_time; $i <= $limit_to_time; $i++){

                                $cDate = $thisYear . '-' . $thisMonth . "-" . sprintf("%02d", $i);
                                $cTimeSheet = null;
                                foreach ($mTimeSheet AS $ts1) {
                                    if (!$ts1['user_id'])
                                        continue;

                                    //Nếu setuid, thì có nhiều OrgId cho userid, mỗi OrgId 1 hàng
                                    if ($uidSet) {
                                        if ($ts1['time_frame'] == $cDate && $ts1->org_id == $indexOrOrgId) {
                                            $cTimeSheet = $ts1;
                                            break;
                                        }
                                    } else
                                        if ($ts1['time_frame'] == $cDate && $cuid == $ts1['user_id']) {
                                            $cTimeSheet = $ts1;
                                            break;
                                        }
                                }
                                if ($cTimeSheet instanceof \App\Models\HrTimeSheet) ;
                                if($cTimeSheet){

                                if($cTimeSheet['n_session'] && strstr($cTimeSheet['n_session'], '_')){
                                    $totalHourUser += $cTimeSheet['n_session'] ? explode("_", $cTimeSheet['n_session'])[1] : 0;
                                    $nCa += 1;
                                 }

                                if($cTimeSheet['meal'] && strstr($cTimeSheet['meal'], '_'))
                                    $totalMealUser += $cTimeSheet['meal'] ? explode("_", $cTimeSheet['meal'])[1] : 0;
                                if ($cTimeSheet['n_hour'])
                                    $totalHourUserBonus += $cTimeSheet['n_hour'];

                                $disable = null;
                                if ($limitDayEdit) {
                                    if ($cDate < date("Y-m-d", time() - _NSECOND_DAY * $limitDayEdit)
                                        //|| $cDate > date("Y-m-d", time() + _NSECOND_DAY * 1)
                                    )
                                        $disable = "disabled";
                                }
                                if ($uidSet)
                                    $disable = "disabled";

                                $thu1 = \LadLib\Common\clsDateTime2::getDayOfDateVN(strtotime("$cDate"), "T");

                                $strTimeDateTitle = " | $thu1 - $cDate | ";
                                ?>

                                <div
                                    title="<?php echo " $strTimeDateTitle" ?> , SheetId = <?php if ($cTimeSheet) echo $cTimeSheet['id'] ?>"
                                    data-code-pos='ppp16780511'
                                    data-id="<?php if ($cTimeSheet) echo $cTimeSheet['id'] ?>"
                                    class="divTable2Cell divCellDataForTest <?php
                                    if (in_array($i, $mSun))
                                        echo "sunDay";
                                    if (in_array($i, $mSat))
                                        echo "satDay";
                                    ?>  ">
                                    <?php
                                    //                                    if($i <= $tomorowDay)
                                    {
                                    ?>
                                    <div class="div_one_cell_checkbox" style="display: none; text-align: center">
                                        <input <?php echo $disable ?> type="checkbox" class="one_cell_checkbox"
                                               style="">
                                    </div>
                                    <select title="Val=<?php echo $cTimeSheet['n_session'] . $strTimeDateTitle ?>"
                                            <?php echo $disable ?> style="" class="n_session"
                                            data-code-pos="ppp16654dd25433">
                                        <?php
                                        foreach($mTmpCa AS $i1=>$str){
                                        ?>
                                        <option
                                            value="<?php echo $i1 ?>" <?php if ($cTimeSheet && $i1 == $cTimeSheet['n_session']) echo 'selected' ?> ><?php echo $str; ?></option><?php
                                        }
                                        ?>
                                    </select>
                                    <select title="Val=<?php echo $cTimeSheet['n_hour'] . $strTimeDateTitle  ?>"
                                            <?php echo $disable ?> class="n_hour" data-code-pos="ppp1665433">
                                        <?php
                                        foreach($mTmpHour AS $i1=>$str)
                                        {
                                        ?>
                                        <option
                                            value="<?php echo $i1 ?>"<?php if ($cTimeSheet && $i1 == $cTimeSheet['n_hour']) echo 'selected' ?> > <?php echo $str ?> </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <select title="Val=<?php echo $cTimeSheet['meal'] . $strTimeDateTitle  ?>"
                                            <?php echo $disable ?> data-code-pos="ppp16d25433" class="meal">
                                        <?php
                                        foreach($mTmpMeal AS $i1=>$str){
                                        ?>
                                        <option
                                            value="<?php echo $i1 ?>"<?php if ($cTimeSheet && $i1 == $cTimeSheet['meal']) echo 'selected' ?> > <?php echo $str; ?></option><?php
                                        }
                                        ?>
                                    </select>

                                    <select <?php echo $disable ?> data-code-pos="ppp16611dd25433" class="n_late">
{{--                                        <?php--}}
{{--                                        foreach($mTmpLate AS $i1=>$str){--}}
{{--                                        ?>--}}
{{--                                        <option--}}
{{--                                            value="<?php echo $i1 ?>"<?php if ($cTimeSheet && $i1 == $cTimeSheet['n_late']) echo 'selected' ?> > <?php echo $str; ?></option>--}}
{{--                                        <?php--}}
{{--                                        }--}}
{{--                                        ?>--}}
                                    </select>

                                    <?php
                                    if(0){
                                    ?>

                                    <select <?php echo $disable ?> data-code-pos="ppp16615433" class="late">
                                        <option value="0">-Vi phạm-</option>
                                        <option value="bo_ca_truc"> Bỏ ca trực</option>
                                        <option value="bo_vi_tri_lam_viec">Bỏ vị trí làm việc</option>
                                        <option value="khong_tuan_thu_chi_thi">Không tuân thủ chỉ thị</option>
                                        <option value="quen_cham_van_tay"> Quên chấm vân tay</option>
                                    </select>

                                    <select <?php echo $disable ?> data-code-pos="ppp1644615433" class="dong_phuc">
                                        <option value="0">-Đồng Phục-</option>
                                        <option value="vi_pham_trang_phuc"> Vi phạm</option>
                                    </select>

                                    <select <?php echo $disable ?> data-code-pos="ppp1644615433" class="tac_phong">
                                        <option value="0">-Tác phong-</option>
                                        <option value="tac_phong_yeu_kem">Tác phong yếu kém</option>
                                    </select>

                                    <?php
                                    }
                                    ?>


                                    <div class="div_one_cell_checkbox" style="display: none; text-align: center">
                                        <a target="_blank"
                                           href="/admin/hr-time-sheet/edit/<?php echo $cTimeSheet['id'] ?>"
                                           title="<?php echo $cTimeSheet['id'] ?>">
                                            <i class="fa fa-edit" style="color: #ccc"></i>
                                        </a>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php

                                }

                                }



                                ?>

                                <div data-code-pos='ppp16861247841' class="divTable2Cell total" title="tienDien">
                                    <input type="text">
                                </div>
                                <div data-code-pos='ppp16822447841' class="divTable2Cell total" title="tienDien">
                                    <input type="text">
                                </div>
                                <div data-code-pos='ppp162447841' class="divTable2Cell total" title="tienDien">
                                    <input type="text">
                                </div>

                                <div data-code-pos='ppp1686122241' class="divTable2Cell total" style="min-width: 150px">
                                    <?php
                                    $overTimeHour = $totalHourUser - $fixHourMonth;
                                    if ($overTimeHour < 0)
                                        $overTimeHour = 0;
                                    $totalSalaryUser = $salaryHour * ($totalHourUser - $overTimeHour);
                                    $totalSalaryUserOverTime = $salaryHourOverTime * $overTimeHour;
                                    $totalMoneyMeal = $totalMealUser * $priceMeal;

                                    echo "\n Số ca: <b> $nCa ($totalHourUser </b> giờ)";
                                    echo "<br/>\n Giờ làm thêm: <b> $totalHourUserBonus giờ </b>";
                                    //                                    echo "\n Lương: $totalSalaryUser k + $totalSalaryUserOverTime k = " . ($totalSalaryUser  + $totalSalaryUserOverTime ) . "k";
                                    echo "\n<br> <b> $totalMealUser </b> bữa";

                                    $totalHourAll += $totalHourUser;
                                    $totalMealAll += $totalMealUser;

                                    ?>
                                </div>


                            </div>

                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="" style="text-align: left; padding: 10px; font-size: large">
                        <?php
                        echo "\n Tổng hợp Số giờ làm: $totalHourAll,  Số bữa ăn $totalMealAll";
                        ?>
                    </div>

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

        let user_token = jctool.getCookie('_tglx863516839');

        $(".divTable2Cell select").each(function () {

            if ($(this).val() != 0) {

                $(this).css("color", 'red');
                if ($(this).hasClass('meal'))
                    $(this).css("color", 'blue');
                if ($(this).hasClass('n_session'))
                    $(this).css("color", 'green');


                // $(this).css("font-weight", 'bolder');
            }

        })


        $("#btn_set_multi_meal , #btn_set_multi_n_session , #btn_set_multi_n_hour").on('click', function () {

            console.log("set_value_multi ", this.id);
            let idBtn = this.id

            $(".divTable2Row select").css("border", '0px solid red');
            $('.select_one_check').each(function () {
                if (!this.checked)
                    return;


                let uidP = $(this).parents(".divTable2Row").attr("data-uid")
                console.log(" uidP ", uidP);

                let oneRow = $(".divTable2Row[data-uid=" + uidP + "]");

                let setVal = ''
                if (idBtn === 'btn_set_multi_meal') {
                    setVal = $("#set_multi_meal").val()
                    oneRow.find('select.meal').val(setVal);
                    oneRow.find('select.meal').prop('value', setVal);
                    oneRow.find('select.meal').attr('value', setVal);
                    oneRow.find('select.meal').css("border", '1px solid red');
                } else if (idBtn === 'btn_set_multi_n_session') {
                    setVal = $("#set_multi_n_session").val()
                    oneRow.find('select.n_session').val(setVal);
                    oneRow.find('select.n_session').prop('value', setVal);
                    oneRow.find('select.n_session').attr('value', setVal);
                    oneRow.find('select.n_session').css("border", '1px solid red');
                } else if (idBtn === 'btn_set_multi_n_hour') {

                    setVal = $("#set_multi_n_hour").val()
                    console.log("Set set_multi_n_hour = ", setVal);

                    oneRow.find('select.n_hour').val(setVal);
                    oneRow.find('select.n_hour').prop('value', setVal);
                    oneRow.find('select.n_hour').attr('value', setVal);
                    oneRow.find('select.n_hour').css("border", '1px solid red');
                }

            })


            $('.one_cell_checkbox').each(function () {
                if ($(this).is(':checked')) {
                    let dtId = $(this).parents('.divTable2Cell').attr('data-id')
                    console.log("OK checked...", dtId);
                    if (idBtn === 'btn_set_multi_meal') {
                        let setVal = $("#set_multi_meal").val()
                        let oneRow = $(this).parents('.divTable2Cell').find("select.meal")
                        oneRow.val(setVal);
                        oneRow.css("border", '1px solid red')
                    }
                    if (idBtn === 'btn_set_multi_n_session') {
                        let setVal = $("#set_multi_n_session").val()
                        let oneRow = $(this).parents('.divTable2Cell').find("select.n_session");
                        oneRow.val(setVal);
                        oneRow.css("border", '1px solid red')
                    }
                    if (idBtn === 'btn_set_multi_n_hour') {
                        let setVal = $("#set_multi_n_hour").val()
                        let oneRow = $(this).parents('.divTable2Cell').find("select.n_hour")
                        oneRow.val(setVal);
                        oneRow.css("border", '1px solid red')
                    }
                }
            })

        })

        $("#save_all_table").on('click', function () {


            let mPost = [];
            let nItem = 0
            $(".divTable2Cell").each(function () {

                let dataId = $(this).attr("data-id");

                if (!dataId)
                    return;

                nItem++

                let n_session = $(this).find('.n_session').val()
                let n_hour = $(this).find('.n_hour').val()
                let meal = $(this).find('.meal').val()
                let n_late = $(this).find('.n_late').val()
                // console.log(" n_session n_hour meal = ",dataId, n_session, n_hour, meal);

                mPost.push({name: 'id[]', value: dataId})
                mPost.push({name: 'n_session[]', value: n_session})
                mPost.push({name: 'n_hour[]', value: n_hour})
                mPost.push({name: 'meal[]', value: meal})
                mPost.push({name: 'n_late[]', value: n_late})
            })

            console.log("mPostX = ", nItem, mPost);

            let url = '/api/hr-time-sheet/update-multi';
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

        $(".divTable2Cell select").on("change", function () {

            //không thay đổi từng cái, mà save all mới thay đổi
            return;

            let uid = $(this).parents(".divTable2Row").attr("data-uid")

            console.log("UID = ", uid);

            if ($(this).val() == -1) {
                $(this).css("color", 'red');
                $(this).css("font-weight", 'bolder');
            }

            if ($(this).val() == -2) {
                $(this).css("color", 'blue');
                $(this).css("font-weight", 'bolder');
            }

            let fieldChange = '';
            if ($(this).hasClass('meal')) {
                console.log("Đổi bữa ", $(this).val());
                fieldChange = 'meal'
            }

            if ($(this).hasClass('n_hour')) {
                console.log("Đổi giờ ", $(this).val());
                fieldChange = 'n_hour'
            }

            if ($(this).hasClass('session')) {
                console.log("Đổi ca ", $(this).val());
                fieldChange = 'n_session'
            }

            let dataPost = {}
            dataPost[fieldChange] = $(this).val();

            let dataId = $(this).parents(".divTable2Cell").attr("data-id");


            let url = '/api/hr-time-sheet/update/' + dataId;
            showWaittingIcon()
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                data: dataPost,
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

        $(".one_cell_checkbox").on("click", function (e) {
            e.stopPropagation()
        })

        $("#btn_show_check_box").on("click", function (e) {


            $('.div_one_cell_checkbox').toggle();

            if ($('.one_cell_checkbox').is(":visible")) {
                console.log("Visible ...");
            } else {
                console.log("Not Visible ...");
                $('.one_cell_checkbox').prop('checked', false);
            }


        })

        $(".divTable2Cell").on("click", function () {
            if ($(this).find('.one_cell_checkbox').is(':visible')) {

                console.log("OK visi...");


                let checkb = $(this).find('.one_cell_checkbox')

                if (checkb.attr('disabled') || checkb.prop('disabled'))
                    return;

                checkb.prop("checked", !checkb.prop("checked"));
            }
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



        $(function () {
            $(document).tooltip();

        });


    </script>



@endsection

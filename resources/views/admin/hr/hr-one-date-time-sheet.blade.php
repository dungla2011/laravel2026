<?php
//dump($mUserAdminTree);

//Set Tree ID
$setTreeId = request('tree_id');

if(!$setTreeId)
    die("Not set id tree?");

//$setTreeId = 11;
$setUidMng = getCurrentUserId();
//$setUidMng = 10411;
$onlyDate = request('date_only');
if(isAdminACP_())
    $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayTreeWithAdminUid($setUidMng);
else
    $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayUserManageTree();
$treeInfo = null;
if($setTreeId){
    $treeInfo = \App\Models\HrOrgTree::find($setTreeId);
    if(!$treeInfo){
        die("Not found org id: $setTreeId");
    }
}
?>

@php
    $template = "layouts.adm";
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

        .content-wrapper>.content {
            padding: 0px;
        }

    </style>
    <style media="print">
        @page { size: portrait; }
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


        .divTable2Heading1 .divTable2Cell{
            background-color: snow;
        }
        .divTable2Heading1{
            background-color: #eee;
        }



        .divTable2Cell.total{
            font-size: small;
            padding: 5px;
        }

        .divTable2Cell + .sunDay {
            background-color: lavender;
        }

        .divTable2Cell + .satDay {
            background-color: lavenderblush;
        }



        .divTable2Cell span.center{
            text-align: center;
            display: block;
        }
    </style>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?php

        $fixHourMonth = 360;
        $salaryHour = 20;
        $salaryHourOverTime = 25;
        $priceMeal = 25;

//        $lastDateMonth = date("t", strtotime($timeString));
//        if($thisMonth < date("m")){
//            $lastDateMonth = date("t", strtotime("$thisYear-$thisMonth"));
//            $thisDay = $lastDateMonth;
//        }

        $userid = $setUidMng;

        //Liệt kê các member thuộc Tree hiện tại này và insert TimeSheet cho từng user
        //Ngoài ra là các member có thể không còn thuộc tree do được move sang tree khác, thì vẫn phải còn data cũ ngày cũ chấm công trước đó
//        $mNhanVienThuocTree = \App\Models\HrEmployee::where(["parent_id" => $setTreeId])->whereRaw("user_id > 0")->get();
        $mNhanVienThuocTree = \App\Models\HrEmployee::where(["parent_id" => $setTreeId])->whereRaw("user_id > 0")->orderBy('orders','ASC')->get();
        if($mNhanVienThuocTree instanceof \Illuminate\Database\Eloquent\Collection);
        $mIdUser1 = array_map(function ($obj){
            if(\App\Models\User::find($obj['user_id']))
                return $obj['user_id'];
        },$mNhanVienThuocTree->toArray());

        //Lấy ra các UID của all timesheet tháng này:
        $mNhanVienDaInsertVaoTimeSheet = App\Models\HrTimeSheet::select('user_id')->distinct()
            ->where('org_id', $setTreeId)
            ->where("date", $onlyDate)
            ->get();

        $mIdUser2 = array_map(function ($obj){
            if(\App\Models\User::find($obj['user_id']))
                return $obj['user_id'];
        },$mNhanVienDaInsertVaoTimeSheet->toArray());

        //Nhập danh sách user 2 bên lại
        $mIdUserAll = array_unique(array_merge($mIdUser1, $mIdUser2));
        $mIdUserAll = array_filter($mIdUserAll);

        //Insert Data
        $mTimeSheet = [];

        foreach ($mIdUserAll AS $uid) {
            //Bắt buộc phải có UID
//                if(!$obj->user_id)
//                    continue;
            if ($hrTimeS = \App\Models\HrTimeSheet::where(['org_id'=>$setTreeId, "user_id" => $uid, 'date' => $onlyDate])->first()) {
                $mTimeSheet[] = $hrTimeS;
            }
        }

        ?>

        <div class="content time_sheet">
            <div class="container-fluid">
                <div class="col-md-12" style="padding-top: 20px">
                    <div STYLE="margin: 20px 0px; text-align: center; ">
                        <b style="font-size: x-large">
                        CHẤM CÔNG NGÀY <?php echo "$onlyDate" ?>
                        </b>

                        <br>
                        <b>
                        Bộ phận:
                        <?php
                        echo $treeInfo->name
                        ?>
                        </b>

                        <button style="float: right" class="btn btn-sm btn-info hide_n_hour_zero">Ẩn Số giờ = 0</button>
                    </div>
                    <div class="divTable2 divContainer">
                        <div class="divTable2Body">
                            <div class="divTable2Row divTable2Heading1">

                                <div class="divTable2Cell cellHeader">
                                    STT
                                </div>
                                <div class="divTable2Cell cellHeader">
                                    Họ Tên
                                </div>
                                <div class="divTable2Cell cellHeader">
                                    Ca
                                </div>
                                <div class="divTable2Cell cellHeader">
                                    Giờ làm
                                </div>
                                <div class="divTable2Cell cellHeader">
                                    Bữa ăn
                                </div>

                                <div class="divTable2Cell cellHeader">Tổng hợp
                                <?php

                                ?>
                                </div>

                            </div>
                            <?php

                            $totalHourAll = $totalMealAll = 0;

                            $cc = 0;
                            foreach ($mIdUserAll AS $cuid){

                                $obj = \App\Models\User::find($cuid);
                                if(!$obj)
                                    continue;

                            ?>


                            <div data-code-pos='ppp16861051978811' class="divTable2Row" data-uid="<?php echo $cuid ?>">


                                <div class="divTable2Cell text-center" style="">
                                    <span class="stt">
                                    <?php
                                    echo ++$cc;
                                    ?>
                                    </span>
                                </div>
                                <div class="divTable2Cell text-left" style="min-width: 130px; padding: 5px; font-size: small">

                                        <?php

                                        $hrUser = \App\Models\HrEmployee::where("user_id", $cuid)->first();

                                        $hrTitle = \App\Models\HrJobTitle::find($hrUser->job_title);



                                        echo " <b> $hrUser->last_name $hrUser->first_name </b> " .
                                            "<br><a target='_blank' href='/admin/hr-employee/edit/$hrUser->id'> Mã Ns: $cuid </a><br> $hrTitle?->name";
                                        ?>

                                </div>


                                <?php

                                $totalHourUser = 0;
                                $totalHourUserPlus = 0;
                                $totalCa = 0;
                                $totalMealUser = 0;
                                $cDate = $onlyDate;
                                $cTimeSheet = null;
                                foreach ($mTimeSheet AS $ts1) {
                                    if(!$ts1->user_id)
                                        continue;
                                    if ($ts1->date == $cDate && $cuid == $ts1->user_id) {
                                        $cTimeSheet = $ts1;
                                        break;
                                    }
                                }
                                if($cTimeSheet instanceof \App\Models\HrTimeSheet);
                                if($cTimeSheet){
                                    if($cTimeSheet->n_session && strstr($cTimeSheet->n_session , '_')){
                                        $totalCa++;
                                        $totalHourUser += explode("_", $cTimeSheet->n_session)[1];
                                    }

                                    if($cTimeSheet->n_hour && $cTimeSheet->n_hour > 0)
                                        $totalHourUserPlus += $cTimeSheet->n_hour;

                                    $nMeal = 0;
                                    if($cTimeSheet->meal && strstr($cTimeSheet->meal , '_'))
                                        $totalMealUser += $nMeal = explode("_", $cTimeSheet->meal)[1];

                                }
                                ?>
                                <div data-code-pos='ppp168610511' class="divTable2Cell">
                                    <span class="center">
                                    <?php
                                        if(strstr($cTimeSheet->n_session, '_'))
                                            echo \App\Models\HrTimeSheet_Meta::FgetSessionCaArrayTimeSheet()[$cTimeSheet->n_session]
                                    ?>
                                    </span>
                                </div>
                                <div data-code-pos='ppp168610511' class="divTable2Cell">
                                    <span class="center n_hour" data-val="<?php echo $cTimeSheet->n_hour ?>">
                                    <?php
                                    echo $cTimeSheet->n_hour
                                    ?>
                                    </span>
                                </div>
                                <div data-code-pos='ppp168610511' class="divTable2Cell">
                                    <span class="center">
                                    <?php

                                    echo $nMeal
                                    ?>
                                    </span>
                                </div>
                                <div data-code-pos='ppp16861222841' class="divTable2Cell total" style="min-width: 150px">
                                    <?php

                                    $totalSalaryUser = $salaryHour * ($totalHourUser);
                                    $totalSalaryUserOverTime = $salaryHourOverTime * $totalHourUserPlus;
                                    $totalMoneyMeal = $totalMealUser * $priceMeal;

                                    echo "\n Số ca: $totalCa ($totalHourUser giờ)";
                                    echo "\n Vượt giờ: $totalHourUserPlus  giờ";
                                    echo "<br/>\n";
                                    echo "\n Lương: $totalSalaryUser k + $totalSalaryUserOverTime k = " . ($totalSalaryUser  + $totalSalaryUserOverTime ) . "k";
                                    echo "\n<br> Bữa: $totalMealUser ";

                                    $totalHourAll+=$totalHourUser;
                                    $totalMealAll+=$totalMealUser;

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

                </div>

            </div>
        </div>
    </div>
    <!-- /.content -->



@endsection


@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(base_path() . "/public/admins/table_mng.js") ?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(base_path() . "/public/vendor/div_table2/div_table2.js") ?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

    <script>
        $(".hide_n_hour_zero").on("click", function (){

            $("span.n_hour").each(function (){
                if($(this).attr('data-val') == 0){
                    $(this).parents(".divTable2Row").toggle();
                }
            })

            let cc = 0;
            $(".divTable2Row").each(function (){

                // if($(this).find("span.n_hour").attr('data-val') != 0)
                if($(this).is(":visible"))
                {
                    $(this).find("span.stt").html(cc)
                    cc++;
                }
            })

        })

    </script>

@endsection

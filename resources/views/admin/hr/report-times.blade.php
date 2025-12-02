<?php
$orgId = 0;
$setTreeId = $orgId = request("org_id");
$uidSet = request('user_id_set');
?>
@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

@section("title")

    HR - Bảng công tổng hợp - Report <?php
    ?>

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path() . '/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path() . '/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet"
          href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path() . '/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">
    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style>
        @media print {
            @page {
                size: landscape
            }

            .main-footer {
                display: none;
            }

            .hide_info {
                display: none;
            }
        }

        .one_cell div {
            font-size: x-small;
            color: #ccc;
        }

        .one_cell .sum {
            font-size: x-small;
            color: black !important;
        }

        .one_cell div {
            border-bottom: 0px solid #ddd;
        }


        .one_cell .red {
            color: red
        }

        .one_cell .blue {
            color: blue
        }

        .one_cell .green {
            color: green
        }

        .one_cell .pink {
            color: purple;
        }

        .one_cell .black {
            color: black;
        }

        .one_cell .bold {
            font-weight: bold;

        }

        .hide1 {
            display: none;
        }

        .div_user_id_info:hover > * {
            display: block;
        }

        .sunDay {
            background-color: lavender !important;
        }

        .satDay {
            background-color: lavenderblush !important;
        }


    </style>

@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">

            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <button id="hide_session" class="hide_info btn btn-sm btn-primary"> Ẩn Ca</button>
                <button id="hide_hour" class="hide_info btn btn-sm btn-info"> Ẩn Giờ thêm</button>
                <button id="hide_meal" class="hide_info btn btn-sm btn-primary"> Ẩn Bữa ăn</button>
                <button id="hide_late" class="hide_info btn btn-sm btn-info"> Ẩn Giờ muộn</button>

                <DIV class="text-center mb-3">
                    <H2>
                        TỔNG HỢP CÔNG THÁNG <?php
                        echo request("month");
                        ?>
                    </H2>
                    <h4 style="color: green">
                        <?php
                        if ($orgId) {
                            echo " Mục tiêu: " . \App\Models\HrOrgTree::find($orgId)->name . " ($orgId) ";
                        }
                        if($uidSet){
                            $hrUser = \App\Models\HrEmployee::where("user_id", $uidSet)->first();
                            if ($hrUser)
                                echo "Thành viên: $hrUser?->last_name $hrUser?->first_name ($uidSet)";
                        }
                        ?>
                    </h4>
                </DIV>

                <?php

                use App\Models\HrCommon;

                //Nếu có set orgid:
                $orgId = 0;
                if ($orgId = request("org_id")) {
                    $setTreeId = $orgId;
                    $mMonth = [];
                    $mUid = HrCommon::getUsersIdInOrgId($orgId);
                    foreach ($mUid AS $x) {
                        $mMonth[] = request('month');
                    }

                    if (!$mUid) {
                        bl("Bạn chưa chọn thành viên để xuất bảng lương!");
                        echo "<br/>\n";
                    }

                    if (!$mMonth) {
                        bl("Bạn chưa chọn tháng để xuất bảng lương!");
                        echo "<br/>\n";
                    }
                }



                if($uidSet){
                    $orgId = 0;
//                    foreach ($mUid AS $x) {
//                        $mMonth[] = request('month');
//                    }
                }

                $thisMonth0 = request("month");
                list($thisYear, $thisMonth) = explode("-", $thisMonth0);

                $timeString = "$thisYear-$thisMonth";
                $lastDateMonth = date("t", strtotime($timeString));
                \Illuminate\Support\Facades\DB::enableQueryLog();

                if($uidSet){
                    $mmTimeSheet = \App\Models\HrTimeSheet::where("user_id", $uidSet)->where('time_frame', '>=', "$thisYear-$thisMonth-01")->where('time_frame', '<', "$thisYear-$thisMonth-32")->get()->toArray();
                    $mmTimeSheet = json_decode(json_encode($mmTimeSheet));
                    $mUid = [];
                    foreach ($mmTimeSheet AS $tmp1){
                        if($tmp1->org_id)
                        if(!isset($mUid[$tmp1->org_id]))
                            $mUid[$tmp1->org_id] = $tmp1->user_id;
                    }
                }else{
                    $mmTimeSheet = \App\Models\HrTimeSheet::where('org_id', $orgId)->whereIn("user_id", $mUid)->where('time_frame', '>=', "$thisYear-$thisMonth-01")->where('time_frame', '<', "$thisYear-$thisMonth-32")->get()->toArray();
                    $mmTimeSheet = json_decode(json_encode($mmTimeSheet));
                }

                $query = \Illuminate\Support\Facades\DB::getQueryLog();


                $mSun = \LadLib\Common\clsDateTime2::getSunDayInMonth($thisYear, $thisMonth);
                $mSat = \LadLib\Common\clsDateTime2::getSatDayInMonth($thisYear, $thisMonth);


                ?>

                <table class='glx00' style="width: 100%">

                    <tr>

                        <th>STT</th>
                        <th style="min-width: 150px">THÀNH VIÊN</th>

                        <th style="min-width: 55px">MỤC</th>
                        <?php
                        for($i = 1; $i <= $lastDateMonth; $i++){
                        $dateInMonth = sprintf("%02d", $i);
                        $thu = \LadLib\Common\clsDateTime2::getDayOfDateVN(strtotime("$thisYear-$thisMonth-$dateInMonth"), "T");


                        $strDate = "$thisYear-$thisMonth-$dateInMonth";
                        $bgToday = null;
                        if ($strDate == nowy()) {
//                            $bgToday = ";background-color: yellow;";
                        }

                        ?>
                        <td style="<?php echo $bgToday ?>" class="divTable2Cell cellHeader <?php
                        if (in_array($i, $mSun))
                            echo "sunDay";
                        if (in_array($i, $mSat))
                            echo "satDay";
                        ?>   "><?php

                            echo " <div data-code-pos='ppp16879369630811' style='$bgToday; border-bottom: 1px solid black;  display: inline-block'> $dateInMonth </div> " .
                                "<br> $thisMonth <br> <i style='color: gray'> $thu </i> <br> <a target='_blank' href='/admin/hr-cham-cong?tree_id=$setTreeId&date_only=$strDate'> <i style='color: gray' class='fa fa-info-circle'></i> </a> "
                            ?>
                        </td>
                        <?php
                        }
                        ?>


                        <td style="min-width: 150px; text-align: center">
                            <b>
                                TỔNG HỢP
                            </b>
                        </td>

                        <td style="min-width: 100px; text-align: center">
                            <b>
                                KÝ NHẬN
                            </b>
                        </td>
                    </tr>

                    <?php

                    $mNameCa = \App\Models\HrTimeSheet_Meta::FgetSessionCaArrayTimeSheet();
                    $mNameMeal = \App\Models\HrTimeSheet_Meta::FgetMealArrayTimeSheet();

                    $cc = 0;
                    foreach($mUid AS $maybeOrgId=>$uidx)
                    {

                        $objLuong = HrCommon::getTinhLuongUserMonthOrgId($uidx, "$thisYear-$thisMonth", $orgId);


                    $salMonth = HrCommon::getSalaryBaseUser($uidx);

                    $nHourPerSession = HrCommon::getNumberHourPerSessionInMonth($uidx, "$thisYear-$thisMonth");

                    $cc++;
                    ?>
                    <tr>

                        <?php
                        echo "<td>$cc</td>"
                        ?>
                        <td style="font-size: x-small" data-code-pos='ppp16887888068431'> <?php
                            $mt = new \App\Models\HrExtraCostEmployee_Meta();
                            echo $mt->_user_id((object)['month' => "$thisYear-$thisMonth"], $uidx);

                            echo " &nbsp Lương cơ bản: <b> " . number_formatvn0($salMonth) ."</b>";

                            if($uidSet){
                                echo "<br> Mục tiêu: " . \App\Models\HrOrgTree::find($maybeOrgId)->name . " ($maybeOrgId) ";
                            }

                            ?>
                        </td>

                        <td style="font-size: x-small; text-align: left; font-style: italic"
                            data-code-pos='ppp16887888114891'>
                            <div class="sum n_session">Số ca:
                            </div>
                            <div class="sum n_hour">
                                Bữa ăn:
                            </div>
                            <div class="sum meal">
                                Tăng ca:
                            </div>
                            <div class="sum n_late">
                                Muộn:
                            </div>
                        </td>

                        <?php

                        $ttCa = 0;
                        $ttMeal = 0;
                        $ttHour = 0;
                        $ttHourPlus = 0;
                        $ttLateCa = 0;
                        $ttLateHour = 0;


                        for ($i = 1; $i <= $lastDateMonth; $i++) {

                            $dateInMonth = sprintf("%02d", $i);
                            $found = 0;
                            foreach ($mmTimeSheet AS $ts) {

                                if($uidSet && $ts->org_id != $maybeOrgId)
                                    //Nếu theo user, thì TS sẽ lấy theo orgid
                                        continue;
                                //Nếu theo Orgid, thì phải theo userid)
                                elseif($orgId && $ts->user_id != $uidx)
                                        continue;


                                if ($ts->time_frame == "$thisYear-$thisMonth-$dateInMonth")
                                {
                                    $found = 1;

                                    $nCa = 0;
                                    $nCaTxt = '_';
                                    if ($ts->n_session && strstr($ts->n_session, '_')) {
                                        $ttCa++;
                                        $nCa = 1;
                                        $nCaTxt = $mNameCa[$ts->n_session];
                                        $ttHour += explode("_", $ts->n_session)[1];
//                                        if (!$nHourPerSession)
//                                            $nHourPerSession = explode("_", $ts->n_session)[1];
                                    }
                                    //
//
                                    if (!$ts->n_late)
                                        $ts->n_late = 0;
                                    else{
                                        $ttLateCa++;
                                        $ttLateHour += HrCommon::getLateHour($ts->n_late, $nHourPerSession);
                                    }


                                    $classCa = $classHour = $classMeal = $classLate = '';
                                    if ($nCaTxt != '_') {
                                        $classCa = "red bold";
                                    }

                                    if ($ts->n_hour < 0)
                                        $ts->n_hour = 0;
                                    if ($ts->n_hour > 0) {
                                        $classHour = "blue bold";
                                        $ttHourPlus += $ts->n_hour;
                                    }

                                    if ($ts->meal && strstr($ts->meal, "_")) {


                                        $ttMeal += explode("_", $ts->meal)[1];
                                        $classMeal = "pink bold";

                                    }

                                    if ($ts->n_late)
                                        $classLate = "bold black";

                                    $cls = '';
                                    if (in_array($dateInMonth, $mSun))
                                        $cls = "sunDay";
                                    if (in_array($dateInMonth, $mSat))
                                        $cls = "satDay";

                                    $nameMeal = '_';
                                    if($ts->meal && isset($mNameMeal[$ts->meal]))
                                        $nameMeal = $mNameMeal[$ts->meal];

                                    if(!$ts->n_hour)
                                        $ts->n_hour = '_';
                                    if(!$ts->n_late)
                                        $ts->n_late = '_';

                                    echo "<td class='one_cell $cls'>" .
                                        " <div class='n_session $classCa'>$nCaTxt </div> " .
                                        " <div class='meal $classMeal'> " . $nameMeal . " </div> " .
                                        " <div class='n_hour $classHour'> $ts->n_hour" . " </div> " .
                                        " <div class='n_late $classLate'> $ts->n_late" .  "</div> " .
                                        "</td>";
                                    break;
                                }
                            }
                            if (!$found)
                                echo "\n <td> ???  </td>";
                        }


                        $ttSalary = 0;

                        $priceHour = round($salMonth / $lastDateMonth / $nHourPerSession);

                        $totalHourOK = $ttHour + $ttHourPlus - $ttLateHour;

                        $moneyHour =  $totalHourOK * $priceHour;

                        $moneyHourTxt = number_formatvn0($moneyHour);

                        $moneyMeal = $ttMeal * 30000;
                        $moneyMealTxt = number_formatvn0($moneyMeal);
                        $totalSalOK = $moneyMeal + $moneyHour ;
                        $totalSalOKTxt  = number_formatvn0($totalSalOK) ;

                        ?>

                        <td style="font-size: x-small" class="one_cell" data-code-pos='ppp16887888146571'>
{{--                            <div class="sum n_session n_hour">--}}
{{--                                Số ca: <b> <?php ?> </b>--}}
{{--                            </div>--}}

{{--                            <div class="sum meal">--}}
{{--                                Số bữa: <b><?php echo "" ?> </b>--}}
{{--                            </div>--}}

{{--                            <div class="sum n_hour">--}}
{{--                                Tăng ca: <b><?php--}}

{{--                                    ?></b> giờ--}}
{{--                            </div>--}}

{{--                            <div class="sum n_late">--}}
{{--                                <?php--}}
{{--                                echo "Muộn: <b>  </b> buổi, (giờ : <b>  </b>) ";--}}
{{--                                ?>--}}
{{--                            </div>--}}

{{--                            <div class="sum n_total">--}}

{{--                                <b>--}}
{{--                                    Tổng giờ: <b> <?php  ?> </b>--}}
{{--                                    <br>--}}
{{--                                    <span style="color: red">--}}
{{--                                    Lương tổng: <?php echo "...";?>--}}
{{--                                    </span>--}}
{{--                                </b>--}}
{{--                            </div>--}}

                            <?php
                            echo "<pre>";
                            print_r($objLuong);
                            echo "</pre>";
                            ?>


                        </td>

                        <td></td>

                    </tr>
                    <?php
                    }
                    ?>


                </table>


            </div>

            <p></p>
            <b>
                &nbsp;
                Công thức tính lương:


            </b>
            <br>
            <br>
        </section>
        <!-- /.content -->

    </div>


@endsection



@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path() . '/admins/table_mng.js');?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path() . '/vendor/div_table2/div_table2.js');?>"></script>

    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>

    <script>
        $("#hide_session").on("click", function () {
            $(".one_cell .n_session").toggle();
        })
        $("#hide_meal").on("click", function () {
            $(".one_cell .meal").toggle();
        })
        $("#hide_hour").on("click", function () {
            $(".one_cell .n_hour").toggle();
        })
        $("#hide_late").on("click", function () {
            $(".one_cell .n_late").toggle();
        })

    </script>

@endsection
